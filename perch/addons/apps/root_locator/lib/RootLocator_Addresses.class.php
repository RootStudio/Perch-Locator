<?php

/**
 * Class RootLocator_Addresses
 *
 * @author James Wigger <james@rootstudio.co.uk>
 */
class RootLocator_Addresses extends PerchAPI_Factory
{
    /**
     * Addresses table
     *
     * @var string
     */
    protected $table = 'root_locator_addresses';

    /**
     * Primary Key
     *
     * @var string
     */
    protected $pk = 'addressID';

    /**
     * Address index table
     *
     * @var string
     */
    protected $index_table = 'root_locator_index';

    /**
     * Sort column
     *
     * @var string
     */
    protected $default_sort_column = 'addressTitle';

    /**
     * Sort direction
     *
     * @var string
     */
    protected $default_sort_direction = 'ASC';

    /**
     * Factory singular class
     *
     * @var string
     */
    protected $singular_classname = 'RootLocator_Address';

    /**
     * Template namespace
     *
     * @var string
     */
    protected $namespace = 'locator';

    /**
     * Database columns to be excluded from template dynamic fields
     *
     * @var array
     */
    public $static_fields = [
        'addressTitle',
        'addressBuilding',
        'addressStreet',
        'addressTown',
        'addressRegion',
        'addressPostcode',
        'addressCountry'
    ];

    protected $addressDistances = [];

    public function getQueued(array $ids)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE ' . $this->pk . ' IN(' . implode(',', $ids) . ');';
        $rows = $this->db->get_rows($sql);

        return $this->return_instances($rows);
    }

    /**
     * Return results filtered by first character of title column
     *
     * @param string $string
     * @param bool   $Paging
     *
     * @return array|bool|SplFixedArray
     */
    public function filterByTitleChar($string, $Paging = false)
    {
        $regex = '^[' . $string . ']';

        if ($Paging && $Paging->enabled()) {
            $sql = $Paging->select_sql();
        } else {
            $sql = 'SELECT';
        }

        $sql .= ' * FROM ' . $this->table;
        $sql .= ' WHERE `addressTitle` REGEXP ' . $this->db->pdb($regex);

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

        return $this->return_instances($results);
    }

    /**
     * Return results where coordinates have been set
     *
     * @param bool $Paging
     *
     * @return array|bool|SplFixedArray
     */
    public function filterByCoordinates($Paging = false)
    {
        if ($Paging && $Paging->enabled()) {
            $sql = $Paging->select_sql();
        } else {
            $sql = 'SELECT';
        }

        $sql .= ' * FROM ' . $this->table;
        $sql .= ' WHERE `addressLatitude` IS NOT NULL AND `addressLongitude` IS NOT NULL';

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

        return $this->return_instances($results);
    }

    /**
     * Return results where errors have occurred
     *
     * @param bool $Paging
     *
     * @return array|bool|SplFixedArray
     */
    public function filterByErrors($Paging = false)
    {
        if ($Paging && $Paging->enabled()) {
            $sql = $Paging->select_sql();
        } else {
            $sql = 'SELECT';
        }

        $sql .= ' * FROM ' . $this->table;
        $sql .= ' WHERE `addressError` IS NOT NULL';

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

        return $this->return_instances($results);
    }

    /**
     * Return total number of records with coordinates
     *
     * @return int
     */
    public function totalWithCoordinates()
    {
        return $this->db->get_count('
          SELECT COUNT(`addressID`) 
          FROM ' . $this->table . ' 
          WHERE `addressLatitude` IS NOT NULL 
          AND `addressLongitude` IS NOT NULL;'
        );
    }

    /**
     * Return total number of records with errors
     *
     * @return int
     */
    public function totalWithErrors()
    {
        return $this->db->get_count('
          SELECT COUNT(`addressID`) 
          FROM ' . $this->table . ' 
          WHERE `addressError` IS NOT NULL;'
        );
    }

    /**
     * Insert a new record into the database
     *
     * @param array $data
     *
     * @return bool
     */
    public function create($data)
    {
        $data['addressUpdated'] = date('Y-m-d H:i:s');

        return parent::create($data);
    }

    /**
     * Create complex custom queries using the Perch filters API
     *
     * @param array $options
     *
     * @return mixed|string
     */
    public function getCustom(array $options)
    {
        // Category Searching
        if (isset($options['category'])) {
            if (!is_array($options['category'])) {
                $options['category'] = [$options['category']];
            }
            if (PerchUtil::count(($options['category']))) {
                foreach ($options['category'] as &$cat) {
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

        // Template settings
        $set_template = $options['template'];
        $options['template'] = function ($items) use ($set_template) {
            if (isset($set_template) && $set_template != false) {
                $template = 'locator/' . str_replace('locator/', '', $set_template);
            } else {
                $template = 'locator/address_list.html';
            }

            return $template;
        };

        $whereCallback = $this->getFilterWhereCallback($options);
        $templateCallback = $this->getTemplateCallback();

        return $this->get_filtered_listing($options, $whereCallback, $templateCallback);
    }

    /**
     * Add additional search parameters to the filter listing
     *
     * @param array $options
     *
     * @return Closure
     */
    private function getFilterWhereCallback(array $options)
    {
        return function (PerchQuery $query) use ($options) {
            $radius = isset($options['$range']) ? (int) $options['$range'] : 25;

            // Geocode address to get coordinates
            if (isset($options['address'])) {
                $Geocoder = RootLocator_GeocoderFactory::createGeocoder();
                $result = $Geocoder->geocode($options['address']);

                if ($result->hasError()) {
                    $query->where[] = ' `addressID` IN(-1)';
                } else {
                    $coordinates = $result->getFirstCoordinates();

                    $include = $this->findAddressesByCoordinate($coordinates['latitude'], $coordinates['longitude'], $radius);

                    $query->where[] = ' `addressID` IN(' . implode(',', $include) . ')';
                }
            }

            // Plain coordinates
            if (isset($options['coordinates']) && is_array($options['coordinates'])) {
                list($lat, $lng) = $options['coordinates'];

                $include = $this->findAddressesByCoordinate($lat, $lng, $radius);

                $query->where[] = ' `addressID` IN(' . implode(',', $include) . ')';
            }

            // Exclude item
            if(isset($options['exclude'])) {
                $query->where[] = ' `addressID` <> ' . (int) $this->db->pdb($options['exclude']);
            }

            // Limit
            $query->where[] = ' `addressLatitude` IS NOT NULL AND `addressLongitude` IS NOT NULL';

            return $query;
        };
    }

    /**
     * Template callback for injecting distance from coordinate search
     *
     * @return Closure
     */
    private function getTemplateCallback()
    {
        return function ($rows) {
            if (count($this->addressDistances) === 0) {
                return $rows;
            }

            foreach ($rows as &$address) {
                if (isset($this->addressDistances[$address['addressID']])) {
                    $distance = round($this->addressDistances[$address['addressID']], 1);
                    $address['addressDistance'] = $distance;
                }
            }

            usort($rows, function ($a, $b) {
                if (isset($a['addressDistance']) && isset($b['addressDistance'])) {
                    return ($a['addressDistance'] < $b['addressDistance']) ? -1 : 1;
                }
            });

            return $rows;
        };
    }

    /**
     * Return address IDs by coordinates within radius
     *
     * @param float $lat
     * @param float $lng
     * @param int   $radius
     *
     * @return array
     */
    private function findAddressesByCoordinate($lat, $lng, $radius = 25)
    {
        $sql = '
            SELECT distinct *,
            ( 3959 * acos( cos( radians( ' . $this->db->pdb($lat) . ' ) ) * cos( radians( `addressLatitude` ) ) * cos( radians( `addressLongitude` ) - radians( ' . $this->db->pdb($lng) . ' ) ) + sin( radians( ' . $this->db->pdb($lat) . ' ) ) * sin( radians( `addressLatitude` ) ) ) ) AS addressDistance
            FROM ' . $this->table . ' HAVING addressDistance <= ' . $this->db->pdb($radius) . '
            ORDER BY addressDistance
        ';

        $rows = $this->db->get_rows($sql);

        $ids = array_map(function ($row) {
            $this->addressDistances[$row['addressID']] = $row['addressDistance'];

            return $row['addressID'];
        }, $rows);

        if (count($ids) > 0) {
            return $ids;
        }

        return [-1];
    }

    /**
     * Return database rows from legacy table format for upgrade
     *
     * @param $Paging
     *
     * @return mixed
     */
    public function getLegacyData($Paging)
    {
        $sql = $Paging->select_sql();

        $sql .= '
            loc.locationTitle, 
            loc.locationBuilding, 
            loc.locationStreet, 
            loc.locationTown, 
            loc.locationRegion, 
            loc.locationCountry, 
            loc.locationPostcode, 
            loc.locationDynamicFields,
            mkr.markerLatitude,
            mkr.markerLongitude,
            err.errorMessage
            FROM perch2_jw_locator_locations loc
            LEFT JOIN perch2_jw_locator_markers mkr ON loc.markerID = mkr.markerID
            LEFT JOIN perch2_jw_locator_failed_jobs err ON loc.locationID = err.locationID
        ';

        $sql .= ' ' . $Paging->limit_sql();

        $results = $this->db->get_rows($sql);
        $Paging->set_total($this->db->get_count($Paging->total_count_sql()));

        return $results;
    }
}
