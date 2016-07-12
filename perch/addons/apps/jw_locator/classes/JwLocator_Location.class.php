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
     * Location in queue
     * 
     * @var int
     */
    const STATUS_QUEUED = 1;

    /**
     * Location is being processed
     * 
     * @var int
     */
    const STATUS_PROCESSING= 2;

    /**
     * Location has completed geo-coding
     * 
     * @var int
     */
    const STATUS_SYNCED = 3;

    /**
     * Location failed to be geo-coded
     * 
     * @var int
     */
    const STATUS_FAILED = 4;

    /**
     * Default state is errored
     * 
     * @var int
     */
    const STATUS_ERROR = 0;

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

    /**
     * Convert class instance to generic array with category data for JSON
     *
     * @return array
     */
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

                if($Category) {
                    $out['locationCategories'][$Category->catSlug()] = $Category->catTitle();
                }
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
     * Return if in queue
     *
     * @return bool
     */
    public function is_queued()
    {
        return (int)$this->locationProcessingStatus() === self::STATUS_QUEUED;
    }

    /**
     * Return if location is being geo-coded
     *
     * @return bool
     */
    public function is_processing()
    {
        return (int)$this->locationProcessingStatus() === self::STATUS_PROCESSING;
    }

    /**
     * Return if location is completed
     *
     * @return bool
     */
    public function is_synced()
    {
        return (int)$this->locationProcessingStatus() === self::STATUS_SYNCED;
    }

    /**
     * Return if location failed
     *
     * @return bool
     */
    public function is_failed()
    {
        return (int)$this->locationProcessingStatus() === self::STATUS_FAILED;
    }

    /**
     * Return if location has unknown error
     *
     * @return bool
     */
    public function is_error()
    {
        return (int)$this->locationProcessingStatus() === self::STATUS_ERROR;
    }

    /**
     * Return job status code
     *
     * @deprecated v1.1.1
     * @return int
     */
    public function get_status()
    {
        return (int)$this->locationProcessingStatus();
    }

    /**
     * Return human readable status
     *
     * @deprecated v1.1.1
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
        $API = new PerchAPI('jw_locator', 1.0);
        $Settings = $API->get('Settings');
        $HTML = $API->get('HTML');

        if(!$Settings->get('jw_locator_google_api_key')->val()) {
            return $HTML->warning_message('You must set a Google API key and enable the Maps Embed API to display a preview.');
        }

        $Markers = new JwLocator_Markers($this->api);
        $Marker = $Markers->find($this->markerID());

        $base_url = 'https://maps.googleapis.com/maps/api/staticmap?';
        $parameters = array(
            'size'    => '400x200',
            'maptype' => 'roadmap',
            'markers' => 'color:red|' . $Marker->markerLatitude() . ',' . $Marker->markerLongitude(),
            'key' => $Settings->get('jw_locator_google_api_key')->val()
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
