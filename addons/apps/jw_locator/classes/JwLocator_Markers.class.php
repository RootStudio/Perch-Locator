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
     * Fetch a list of locations using the Haversine formula
     *
     * @param string $address
     * @param int $radius
     * @param int $limit
     * @return array
     */
    public function find_by_address($address, $radius = 50, $limit = false)
    {
        $cache = JwLocator_QueryCache::fetch();
        $cache_slug = PerchUtil::urlify($address);

        if(!$cache->has($cache_slug . '_geocode_result')) {
            $cache->set($cache_slug . '_geocode_result', JwLocator_Geocode::geocode($address));
        }

        $response = $cache->get($cache_slug  . '_geocode_result');

        if ($response['status'] === 'OK') {
            $lat = $response['results'][0]['geometry']['location']['lat'];
            $lng = $response['results'][0]['geometry']['location']['lng'];

            $sql = "SELECT distinct *,
                    ( 3959 * acos( cos( radians( {$lat} ) ) * cos( radians( `markerLatitude` ) ) * cos( radians( `markerLongitude` ) - radians( {$lng} ) ) + sin( radians( {$lat} ) ) * sin( radians( `markerLatitude` ) ) ) ) AS markerDistance
                    FROM `{$this->table}` HAVING markerDistance <= {$radius}
                    ORDER BY markerDistance";

            $query_cache_slug = $cache_slug . '_query_result_' . $radius;

            if($limit) {
                $sql .= " LIMIT {$limit}";
                $query_cache_slug .= '_' . $limit;
            }

            if(!$cache->has($query_cache_slug)) {
                $cache->set($query_cache_slug, $this->db->get_rows($sql));
            }

            $rows = $cache->get($query_cache_slug);
            return $this->return_instances($rows);

        } else {
            return array();
            PerchUtil::debug($response['status']);
        }
    }
}
