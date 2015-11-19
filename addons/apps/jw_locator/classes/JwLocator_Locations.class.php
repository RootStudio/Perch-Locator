<?php

/**
 * Class JwLocator_Locations
 *
 * @author James Wigger <james.s.wigger@gmail.com>
 */
class JwLocator_Locations extends PerchAPI_Factory
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
     * Sort column
     *
     * @var string
     */
    protected $default_sort_column = 'locationTitle';

    /**
     * Sort direction
     *
     * @var string
     */
    protected $default_sort_direction = 'DESC';

    /**
     * Factory singular class
     *
     * @var string
     */
    protected $singular_classname = 'JwLocator_Location';

    /**
     * Template namespace
     *
     * @var string
     */
    protected $namespace = 'locator';

    /**
     * Non dynamic fields
     *
     * @var array
     */
    public $static_fields = array(
        'locationTitle',
        'locationBuilding',
        'locationStreet',
        'locationTown',
        'locationRegion',
        'locationCountry',
        'locationPostcode',
        'locationUpdatedAt',
        'locationProcessedAt',
        'markerID'
    );

    /**
     * Fetch locations in array of IDs
     *
     * @param array $ids
     * @param bool|false $Paging
     * @return array|bool
     */
    public function all_in_array($ids, $Paging = false)
    {
        $results = array();

        if (PerchUtil::count($ids)) {
            if ($Paging && $Paging->enabled()) {
                $sql = $Paging->select_sql();
            } else {
                $sql = 'SELECT';
            }

            $sql .= ' *
                FROM ' . $this->table;

            $sql .= ' WHERE ' . $this->pk . ' IN(' . implode(',', $ids) . ')';

            if (isset($this->default_sort_column)) {
                $sql .= ' ORDER BY ' . $this->default_sort_column . ' ' . $this->default_sort_direction;
            }

            if ($Paging && $Paging->enabled()) {
                $sql .= ' ' . $Paging->limit_sql();
            }

            $results = $this->db->get_rows($sql);

            if ($Paging && $Paging->enabled()) {
                $Paging->set_total($this->db->get_count($Paging->total_count_sql()));
            }
        }

        return $this->return_instances($results);
    }

    /**
     * Eager loading of locations using Marker IDs
     *
     * @param $marker_ids
     * @param bool|false $Paging
     * @return array|bool
     */
    public function all_with_markers($marker_ids, $Paging = false)
    {
        $results = array();

        if (PerchUtil::count($marker_ids)) {
            if ($Paging && $Paging->enabled()) {
                $sql = $Paging->select_sql();
            } else {
                $sql = 'SELECT';
            }

            $sql .= ' *
                FROM ' . $this->table;

            $sql .= ' WHERE ' . $this->pk . ' IN(' . implode(',', $marker_ids) . ')';

            if (isset($this->default_sort_column)) {
                $sql .= ' ORDER BY ' . $this->default_sort_column . ' ' . $this->default_sort_direction;
            }

            if ($Paging && $Paging->enabled()) {
                $sql .= ' ' . $Paging->limit_sql();
            }

            $results = $this->db->get_rows($sql);

            if ($Paging && $Paging->enabled()) {
                $Paging->set_total($this->db->get_count($Paging->total_count_sql()));
            }
        }

        return $this->return_instances($results);
    }

    /**
     * Fetch queued locations in date order
     *
     * @param int $batch_limit
     * @return array|bool
     */
    public function get_queued($batch_limit = 25)
    {
        $sql = 'SELECT * FROM ' . $this->table;
        $sql .= ' WHERE `locationUpdatedAt` > `locationProcessedAt`';
        $sql .= ' ORDER BY `locationUpdatedAt` ASC';
        $sql .= ' LIMIT ' . $batch_limit;

        $results = $this->db->get_rows($sql);

        return $this->return_instances($results);
    }

    /**
     * Create a new record
     *
     * @param array $data
     * @param bool|false $ignore_timestamp
     *
     * @return object|false
     */
    public function create($data, $ignore_timestamp = false)
    {
        if (!$ignore_timestamp) {
            $data['locationUpdatedAt'] = date("Y-m-d H:i:s");
        }

        return parent::create($data);
    }

    /**
     * Search locations with custom filters
     *
     * @param array $opts
     * @return array|bool|mixed|string
     */
    public function get_custom($opts)
    {
        // Category Searching
        if (isset($opts['category'])) {
            if (!is_array($opts['category'])) {
                $opts['category'] = array($opts['category']);
            }

            if (PerchUtil::count(($opts['category']))) {
                foreach ($opts['category'] as &$cat) {
                    if (strpos($cat, '/') === false) {
                        if (substr($cat, 0, 1) == '!') {
                            $cat = '!locator/' . substr($cat, 1) . '/';
                        } else {
                            $cat = 'locator/' . $cat . '/';
                        }
                    }
                }
            }
        }

        // Custom searches
        $where_callback = $this->filtered_listing_where_callback($opts);
        $pre_template_callback = $this->filtered_listing_template_callback($opts);

        // Prepare templates
        $set_template = $opts['template'];
        $opts['template'] = function ($items) use ($set_template) {
            if (isset($set_template) && $set_template != false) {
                $template = 'locator/' . str_replace('locator/', '', $set_template);
            } else {
                $template = 'locator/location.html';
            }

            return $template;
        };

        return $this->get_filtered_listing($opts, $where_callback, $pre_template_callback);
    }

    /**
     * Custom query for address based filtering
     *
     * @param array $opts
     * @return Closure
     */
    private function filtered_listing_where_callback($opts)
    {
        $db = $this->db;

        return function (PerchQuery $Query) use ($opts, $db) {

            // Nearest
            if (isset($opts['address'])) {
                $radius = isset($opts['radius']) ? (int)$opts['radius'] : 25;
                $limit = isset($opts['count']) ? (int)$opts['count'] : false;

                $Markers = new JwLocator_Markers;
                $markers = $Markers->find_by_address($opts['address'], $radius, $limit);

                if (PerchUtil::count($markers)) {
                    $marker_ids = array();

                    foreach ($markers as $Marker) {
                        $marker_ids[] = $Marker->id();
                    }

                    $Query->where[] = ' `markerID` IN(' . implode(',', $marker_ids) . ')';
                } else {
                    $Query->where[] = ' `markerID` IN(-1)';
                }
            }

            return $Query;
        };
    }

    /**
     * Inject data into template data before rendering
     *
     * @param array $opts
     * @return Closure
     */
    private function filtered_listing_template_callback($opts)
    {
        $Markers = new JwLocator_Markers;

        if (isset($opts['address'])) {
            $radius = isset($opts['radius']) ? (int)$opts['radius'] : 25;
            $limit = isset($opts['count']) ? (int)$opts['count'] : false;

            $markers = $Markers->find_by_address($opts['address'], $radius, $limit);
        } else {
            $markers = $Markers->all();
        }

        return function($rows) use($markers) {
            foreach($rows as &$location) {
                $marker_id = $location['markerID'];

                if(is_array($markers)) {
                    $Marker = array_filter($markers, function ($Marker) use ($marker_id) {
                        return $Marker->id() == $marker_id;
                    });

                    $Marker = array_values($Marker);
                }

                if (isset($Marker[0])) {
                    $Marker = $Marker[0];

                    $location['markerLatitude'] = $Marker->markerLatitude();
                    $location['markerLongitude'] = $Marker->markerLongitude();

                    if($distance = $Marker->markerDistance()) {
                        $location['markerDistance'] = round($Marker->markerDistance(), 1);
                    }
                }

                // Remove unwanted data
                unset($location['locationProcessedAt']);
                unset($location['locationProcessingStatus']);
            }

            // Fix distance ordering
            usort($rows, function($a, $b) {
                if(isset($a['markerDistance']) && isset($b['markerDistance'])) {
                    return ($a['markerDistance'] < $b['markerDistance']) ? -1 : 1;
                }
            });

            return $rows;
        };
    }
}
