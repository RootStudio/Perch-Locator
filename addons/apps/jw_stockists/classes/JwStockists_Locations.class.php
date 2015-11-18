<?php

/**
 * Class JwStockists_Locations
 *
 * @author James Wigger <james.s.wigger@gmail.com>
 */
class JwStockists_Locations extends PerchAPI_Factory
{
    /**
     * Locations table
     *
     * @var string
     */
    protected $table = 'jw_stockists_locations';

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
    protected $default_sort_column = 'locationID';

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
    protected $singular_classname = 'JwStockists_Location';

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

        if(PerchUtil::count($ids)) {
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

    public function get_queued($batch_limit = 25)
    {
        $sql  = 'SELECT * FROM ' . $this->table;
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
        if(!$ignore_timestamp) {
            $data['locationUpdatedAt'] = date("Y-m-d H:i:s");
        }

        return parent::create($data);
    }
}
