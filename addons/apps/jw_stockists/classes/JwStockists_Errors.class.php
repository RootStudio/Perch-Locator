<?php

/**
 * Class JwStockists_Errors
 *
 * @author James Wigger <james.s.wigger@gmail.com>
 */
class JwStockists_Errors extends PerchAPI_Factory
{
    /**
     * Failed Jobs Table
     *
     * @var string
     */
    protected $table = 'jw_stockists_failed_jobs';

    /**
     * Primary Key
     *
     * @var string
     */
    protected $pk = 'errorID';

    /**
     * Sort column
     *
     * @var string
     */
    protected $default_sort_column = 'errorID';

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
    protected $singular_classname = 'JwStockists_Error';

    /**
     * Non dynamic fields
     *
     * @var array
     */
    public $static_fields = array(
        'errorID',
        'errorMessage',
        'errorDateTime',
        'locationID'
    );

    /**
     * Find an error associated with a location or create a new one
     *
     * @param int $location_id
     * @param array $data
     * @return bool
     */
    public function find_or_create_by_location($location_id, $data)
    {
        $Error = $this->get_one_by('locationID', $location_id);

        if(is_object($Error)) {
            $Error = $Error->update($data);
        } else {
            $Error = $this->create($data);
        }

        return $Error;
    }

    /**
     * Find an error log associated with a location
     *
     * @param int $location_id
     */
    public function find_by_location($location_id)
    {
        return $this->get_one_by('locationID', $location_id);
    }

    /**
     * Return total errors in DB
     *
     * @return mixed
     */
    public function total()
    {
        return $this->db->get_count('SELECT COUNT(*) FROM ' . $this->table);
    }

    /**
     * Fetch locations that have had an error
     *
     * @param bool|false $Paging
     * @return array|bool
     */
    public function get_locations($Paging = false)
    {
        $rows = $this->db->get_rows('SELECT `locationID` FROM ' . $this->table);

        $location_ids = array();
        if(PerchUtil::count($rows)) {
            foreach($rows as $result) {
                $location_ids[] = (int) $result['locationID'];
            }
        }

        $Locations = new JwStockists_Locations($this->api);
        $locations = $Locations->all_in_array($location_ids, $Paging);

        return $locations;
    }
}
