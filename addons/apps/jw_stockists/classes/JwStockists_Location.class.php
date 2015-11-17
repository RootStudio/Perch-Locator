<?php

include PERCH_PATH . '/addons/apps/jw_stockists/utilities/GoogleMapsGeocoder.php';

class JwStockists_Location extends PerchAPI_Base
{
    protected $table = 'jw_stockists_locations';

    protected $pk = 'locationID';

    public function update($data, $ignore_timestamp = false)
    {
        if (!$ignore_timestamp) {
            $data['locationUpdatedAt'] = date("Y-m-d H:i:s");
        }

        parent::update($data);
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

        if (isset($response['results']) && PerchUtil::count($response['results'])) {
            $marker_data = array();
            $marker_data['markerLatitude'] = $response['results'][0]['geometry']['location']['lat'];
            $marker_data['markerLongitude'] = $response['results'][0]['geometry']['location']['lng'];

            $Markers = new JwStockists_Markers($this->api);
            $Marker = $Markers->find($this->markerID());

            if (!is_object($Marker)) {
                $Marker = $Markers->create($marker_data);
                $this->set_marker($Marker);
            } else {
                $Marker->update($marker_data);
            }

            $this->set_status(3);
        } else {
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

    private function set_status($status_code = 1)
    {
        $this->update(array(
            'locationProcessingStatus' => $status_code
        ), true);
    }

    private function set_marker(JwStockists_Marker $Marker)
    {
        $this->update(array(
            'markerID' => $Marker->id()
        ), true);
    }
}
