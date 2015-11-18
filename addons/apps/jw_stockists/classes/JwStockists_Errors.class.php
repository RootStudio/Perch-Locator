<?php

class JwStockists_Errors extends PerchAPI_Factory
{
    protected $table = 'jw_stockists_failed_jobs';

    protected $pk = 'errorID';

    protected $default_sort_column = 'errorID';

    protected $default_sort_direction = 'DESC';

    protected $singular_classname = 'JwStockists_Error';

    protected $namespace = 'locator';

    public $static_fields = array(
        'errorID',
        'errorMessage',
        'errorDateTime',
        'locationID'
    );

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

    public function find_by_location($location_id)
    {
        return $this->get_one_by('locationID', $location_id);
    }

    public function total()
    {
        return $this->db->get_count('SELECT COUNT(*) FROM ' . $this->table);
    }

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
