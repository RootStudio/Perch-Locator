<?php

include PERCH_PATH . '/addons/apps/jw_stockists/utilities/GoogleMapsGeocoder.php';

class JwStockists_Location extends PerchAPI_Base
{
    protected $table = 'jw_stockists_locations';

    protected $pk = 'locationID';

    public function update($data, $ignore_timestamp = false)
    {
        if (!$ignore_timestamp) {
            $this->set_status(1);
            $data['locationUpdatedAt'] = date("Y-m-d H:i:s");
        }

        $Error = $this->get_error();

        if(is_object($Error)) {
            $Error->delete();
        }

        parent::update($data);
    }

    public function delete()
    {
        $Marker = $this->get_marker();

        if (!is_object($Marker)) {
            $Marker->delete();
        }

        parent::delete();
    }

    public function get_marker()
    {
        $Markers = new JwStockists_Markers($this->api);
        return $Markers->find($this->markerID());
    }

    public function get_error()
    {
        $Errors = new JwStockists_Errors($this->api);
        return $Errors->find_by_location($this->id());
    }

    public function get_status()
    {
        return (int)$this->locationProcessingStatus();
    }

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

        $Geocoder = new GoogleMapsGeocoder($address);
        $response = $Geocoder->geocode();

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

            $this->set_status(3);
        } else {
            $status = $response['status'];

            switch($status) {
                case 'ZERO_RESULTS':
                    $this->set_error('No results found for address');
                    break;
                case 'OVER_QUERY_LIMIT':
                    $this->set_error('API quota limit reached');
                    break;
                case 'REQUEST_DENIED':
                    $this->set_error('Request denied by API');
                    break;
                case 'INVALID_REQUEST':
                    $this->set_error('Request is missing required parameters');
                    break;
                case 'UNKNOWN_ERROR':
                    $this->set_error('External server error');
                default:
                    break;
            }

            $this->set_status(4);
        }
    }

    public function map_preview()
    {
        $Markers = new JwStockists_Markers($this->api);
        $Marker = $Markers->find($this->markerID());

        $base_url = 'https://maps.googleapis.com/maps/api/staticmap?';
        $parameters = array(
            'size' => '400x400',
            'maptype' => 'roadmap',
            'markers' => 'color:red|' . $Marker->markerLatitude() . ',' . $Marker->markerLongitude()
        );

        return '<img src="' . $base_url . http_build_query($parameters) . '" />';
    }

    /**
     * @param int $status_code 1: queued, 2: processing, 3: synced, 4: Failed
     */
    private function set_status($status_code = 1)
    {
        $this->update(array(
            'locationProcessingStatus' => $status_code
        ), true);
    }

    private function set_marker($data)
    {
        $Markers = new JwStockists_Markers($this->api);
        $Marker = $Markers->create($data);

        $this->update(array(
            'markerID' => $Marker->id()
        ), true);
    }

    private function set_error($error_message)
    {
        $data = array();
        $data['errorMessage'] = $error_message;
        $data['errorDateTime'] = date("Y-m-d H:i:s");
        $data['locationID'] = $this->id();

        $Errors = new JwStockists_Errors($this->api);
        $Error = $Errors->find_or_create_by_location($this->id(), $data);

        return $Error;
    }

}
