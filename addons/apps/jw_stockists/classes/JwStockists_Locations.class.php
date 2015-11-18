<?php

class JwStockists_Locations extends PerchAPI_Factory
{
    protected $table = 'jw_stockists_locations';

    protected $pk = 'locationID';

    protected $default_sort_column = 'locationID';

    protected $default_sort_direction = 'DESC';

    protected $singular_classname = 'JwStockists_Location';

    protected $namespace = 'locator';

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

    public function all_in_array($ids, $Paging = false)
    {
        if ($Paging && $Paging->enabled()) {
            $sql = $Paging->select_sql();
        }else{
            $sql = 'SELECT';
        }

        $sql .= ' *
                FROM ' . $this->table;

        $sql .= ' WHERE ' . $this->pk . ' IN('. implode(',', $ids) .')';

        if (isset($this->default_sort_column)) {
            $sql .= ' ORDER BY ' . $this->default_sort_column . ' '.$this->default_sort_direction;
        }

        if ($Paging && $Paging->enabled()) {
            $sql .=  ' '.$Paging->limit_sql();
        }

        $results = $this->db->get_rows($sql);

        if ($Paging && $Paging->enabled()) {
            $Paging->set_total($this->db->get_count($Paging->total_count_sql()));
        }

        return $this->return_instances($results);
    }

    public function create($data, $ignore_timestamp = false)
    {
        if(!$ignore_timestamp) {
            $data['locationUpdatedAt'] = date("Y-m-d H:i:s");
        }

        parent::create($data);
    }
}
