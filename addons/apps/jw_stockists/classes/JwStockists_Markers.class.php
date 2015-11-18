<?php

/**
 * Class JwStockists_Markers
 *
 * @author James Wigger <james.s.wigger@gmail.com>
 */
class JwStockists_Markers extends PerchAPI_Factory
{
    /**
     * Markers table
     *
     * @var string
     */
    protected $table = 'jw_stockists_markers';

    /**
     * Primary Key
     *
     * @var string
     */
    protected $pk = 'markerID';

    /**
     * Sort column
     *
     * @var string
     */
    protected $default_sort_column = 'markerID';

    /**
     * Sort direction
     *
     * @var string
     */
    protected $default_sort_direction = 'DESC';

    /**
     * Factory singluar class
     *
     * @var string
     */
    protected $singular_classname = 'JwStockists_Marker';

    /**
     * Non dynamic fields
     *
     * @var array
     */
    public $static_fields = array(
        'markerID',
        'markerLatitude',
        'markerLongitude'
    );

    public function get_locations()
    {
        $markers = $this->all();

        return $this->eager_loadeding_processor($markers);
    }

    public function get_nearest_locations($address, $radius = 50)
    {
        $response = JwStockists_Geocode::geocode($address);

        if ($response['status'] === 'OK') {
            $lat = $response['results'][0]['geometry']['location']['lat'];
            $lng = $response['results'][0]['geometry']['location']['lng'];

            $sql = "SELECT distinct *,
                    ( 3959 * acos( cos( radians( {$lat} ) ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians( {$lng} ) ) + sin( radians( {$lat} ) ) * sin( radians( `latitude` ) ) ) ) AS distance
                    FROM `{$this->table}` HAVING distance <= {$radius}
                    ORDER BY distance;";

            $rows = $this->db->get_rows($sql);
            $markers = $this->return_instances($rows);

            return $this->eager_loadeding_processor($markers);

        } else {
            return array();
            PerchUtil::debug($response['status']);
        }
    }

    private function eager_loadeding_processor($markers)
    {
        $marker_ids = array();
        if (PerchUtil::count($markers)) {
            foreach ($markers as $Marker) {
                $marker_ids[] = (int)$Marker->id();
            }
        }

        $Locations = new JwStockists_Locations($this->api);
        $locations = $Locations->all_with_markers($marker_ids);

        $locations_data = array();

        if (PerchUtil::count($locations)) {
            foreach ($locations as $Location) {
                $location_id = $Location->id();

                $Marker = array_filter($markers, function ($Marker) use ($location_id) {
                    return $Marker->id() == $location_id;
                });

                $Marker = array_values($Marker);

                if (isset($Marker[0])) {
                    $Marker = $Marker[0];

                    $Location->squirrel('markerLatitude', $Marker->markerLatitude());
                    $Location->squirrel('markerLongitude', $Marker->markerLongitude());

                    $locations_data[] = $Location->to_array();
                }
            }
        }

        return $locations_data;
    }
}
