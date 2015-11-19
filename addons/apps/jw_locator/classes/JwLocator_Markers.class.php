<?php

/**
 * Class JwLocator_Markers
 *
 * @author James Wigger <james.s.wigger@gmail.com>
 */
class JwLocator_Markers extends PerchAPI_Factory
{
    /**
     * Markers table
     *
     * @var string
     */
    protected $table = 'jw_locator_markers';

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
    protected $singular_classname = 'JwLocator_Marker';

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

    /**
     * Fetch a list of locations that have markers
     *
     * @return array
     */
    public function get_locations()
    {
        $markers = $this->all();

        return $this->eager_loading_processor($markers);
    }

    /**
     * Fetch a list of locations using the Haversine formula
     *
     * @param string $address
     * @param int $radius
     * @param int $limit
     * @return array
     */
    public function find_by_address($address, $radius = 50, $limit = false)
    {
        $response = JwLocator_Geocode::geocode($address);

        if ($response['status'] === 'OK') {
            $lat = $response['results'][0]['geometry']['location']['lat'];
            $lng = $response['results'][0]['geometry']['location']['lng'];

            $sql = "SELECT distinct *,
                    ( 3959 * acos( cos( radians( {$lat} ) ) * cos( radians( `markerLatitude` ) ) * cos( radians( `markerLongitude` ) - radians( {$lng} ) ) + sin( radians( {$lat} ) ) * sin( radians( `markerLatitude` ) ) ) ) AS markerDistance
                    FROM `{$this->table}` HAVING markerDistance <= {$radius}
                    ORDER BY markerDistance";

            if($limit) {
                $sql .= " LIMIT {$limit}";
            }

            $rows = $this->db->get_rows($sql);
            return $this->return_instances($rows);

        } else {
            return array();
            PerchUtil::debug($response['status']);
        }
    }

    /**
     * Pair marker data to location instance after eager loading
     *
     * @param $markers
     * @return array
     */
    private function eager_loading_processor($markers)
    {
        $marker_ids = array();
        if (PerchUtil::count($markers)) {
            foreach ($markers as $Marker) {
                $marker_ids[] = (int)$Marker->id();
            }
        }

        $Locations = new JwLocator_Locations($this->api);
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

                    if($distance = $Marker->markerDistance()) {
                        $Location->squirrel('markerDistance', round($Marker->markerDistance(), 1));
                    }

                    $locations_data[] = $Location->to_array();
                }
            }
        }

        return $locations_data;
    }
}
