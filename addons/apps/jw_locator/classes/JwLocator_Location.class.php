<?php

/**
 * Class JwLocator_Location
 *
 * @author James Wigger <james.s.wigger@gmail.com>
 */
class JwLocator_Location extends PerchAPI_Base
{
    /**
     * Locations table
     *
     * @var string
     */
    protected $table = 'jw_locator_locations';

    /**
     * Primary Key
     *
     * @var string
     */
    protected $pk = 'locationID';

    /**
     * Update existing record, optionally forcing a geocode request
     *
     * @param array $data
     * @param bool|false $force_geocoding
     * @param bool|false $ignore_timestamp
     * @return mixed
     */
    public function update($data, $force_geocoding = false, $ignore_timestamp = false)
    {
        if (!$ignore_timestamp) {
            $this->set_status(1);
            $data['locationUpdatedAt'] = date("Y-m-d H:i:s");

            // Move here, as this is a 'normal' update
            $Error = $this->get_error();

            if (is_object($Error)) {
                $Error->delete();
            }
        }

        $result = parent::update($data);

        if ($force_geocoding) {
            $this->geocode();
        }

        return $result;
    }

    /**
     * Delete record including associated marker / error
     */
    public function delete()
    {
        $Marker = $this->get_marker();

        if (is_object($Marker)) {
            $Marker->delete();
        }

        $Error = $this->get_error();

        if (is_object($Error)) {
            $Error->delete();
        }

        parent::delete();
    }

    public function to_array()
    {
        $QueryCache = JwLocator_QueryCache::fetch();
        $Categories = new PerchCategories_Categories();

        $out = parent::to_array();
        $out['locationCategories'] = array();

        if (PerchUtil::count($out['perch_categories'])) {
            foreach ($out['perch_categories'] as $catID) {
                if (!$QueryCache->has('category_search_' . $catID)) {
                    $QueryCache->set('category_search_' . $catID, $Categories->find($catID));
                }

                $Category = $QueryCache->get('category_search_' . $catID);
                $out['locationCategories'][$Category->catSlug()] = $Category->catTitle();
            }
        }

        return $out;
    }

    /**
     * Return associated Marker object
     *
     * @return JwLocator_Marker|false
     */
    public function get_marker()
    {
        $Markers = new JwLocator_Markers($this->api);

        return $Markers->find($this->markerID());
    }

    /**
     * Return associated Error object
     *
     * @return JwLocator_Error|false
     */
    public function get_error()
    {
        $Errors = new JwLocator_Errors($this->api);

        return $Errors->find_by_location($this->id());
    }

    /**
     * Return job status code
     *
     * @return int
     */
    public function get_status()
    {
        return (int)$this->locationProcessingStatus();
    }

    /**
     * Return human readable status
     *
     * @return string
     */
    public function get_status_tag()
    {
        switch ((int)$this->locationProcessingStatus()) {
            case 1:
                return '<span class="tag queued">In Queue</span>';
                break;
            case 2:
                return '<span class="tag processing">Processing</span>';
                break;
            case 3:
                return '<span class="tag synced">Synced</span>';
                break;
            case 4:
                return '<span class="tag error">Failed</span>';
                break;
            default:
                return '<span class="tag error">Error</span>';
                break;
        }
    }

    /**
     * Convert given address into coordinates for Marker object
     */
    public function geocode()
    {
        $this->set_status(2);

        $address = '';
        $address .= $this->locationBuilding();
        $address .= '+' . $this->locationStreet();
        $address .= '+' . $this->locationTown();
        $address .= '+' . $this->locationRegion();
        $address .= '+' . $this->locationPostcode();
        $address .= '+' . $this->locationCountry();

        $response = JwLocator_Geocode::geocode($address);

        if ($response['status'] === 'OK') {
            $marker_data = array();
            $marker_data['markerLatitude'] = $response['results'][0]['geometry']['location']['lat'];
            $marker_data['markerLongitude'] = $response['results'][0]['geometry']['location']['lng'];

            $Marker = $this->get_marker();

            if (!is_object($Marker)) {
                $this->set_marker($marker_data);
            } else {
                $Marker->update($marker_data);
            }

            $this->update(array(
                'locationProcessedAt' => date("Y-m-d H:i:s")
            ), false, true);

            $this->set_status(3);
        } else {
            $status = $response['status'];

            switch ($status) {
                case 'ZERO_RESULTS':
                    $this->set_error('The address could not be found.');
                    break;
                case 'OVER_QUERY_LIMIT':
                    $this->set_error('API quota limit reached.');
                    break;
                case 'REQUEST_DENIED':
                    $this->set_error('Request denied by API.');
                    break;
                case 'INVALID_REQUEST':
                    $this->set_error('Request is missing required parameters');
                    break;
                case 'UNKNOWN_ERROR':
                    $this->set_error('External server error.');
                default:
                    break;
            }

            $this->set_status(4);
        }
    }

    /**
     * Display Google static maps preview
     *
     * @return string
     */
    public function map_preview()
    {
        $Markers = new JwLocator_Markers($this->api);
        $Marker = $Markers->find($this->markerID());

        $base_url = 'https://maps.googleapis.com/maps/api/staticmap?';
        $parameters = array(
            'size'    => '400x200',
            'maptype' => 'roadmap',
            'markers' => 'color:red|' . $Marker->markerLatitude() . ',' . $Marker->markerLongitude()
        );

        return '<img class="map-preview" src="' . $base_url . http_build_query($parameters) . '" />';
    }

    /**
     * Set job status code
     *
     * @param int $status_code 1: queued, 2: processing, 3: synced, 4: Failed
     */
    private function set_status($status_code = 1)
    {
        $this->update(array(
            'locationProcessingStatus' => $status_code
        ), false, true);
    }

    /**
     * Attach Marker object
     *
     * @param array $data
     */
    private function set_marker($data)
    {
        $Markers = new JwLocator_Markers($this->api);
        $Marker = $Markers->create($data);

        $this->update(array(
            'markerID' => $Marker->id()
        ), false, true);
    }

    /**
     * Attach Error object
     *
     * @param string $error_message
     * @return bool
     */
    private function set_error($error_message)
    {
        $data = array();
        $data['errorMessage'] = $error_message;
        $data['errorDateTime'] = date("Y-m-d H:i:s");
        $data['locationID'] = $this->id();

        $Errors = new JwLocator_Errors($this->api);
        $Error = $Errors->find_or_create_by_location($this->id(), $data);

        return $Error;
    }

}
