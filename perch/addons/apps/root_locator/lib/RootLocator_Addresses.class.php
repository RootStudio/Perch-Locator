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
     * Non dynamic fields
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

    public function filterByCoordinates($Paging = false)
    {

    }

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

    public function totalGeocoded()
    {
        return $this->db->get_count('
          SELECT COUNT(`addressID`) 
          FROM ' . $this->table . ' 
          WHERE `addressLatitude` IS NOT NULL 
          AND `addressLongitude` IS NOT NULL;'
        );
    }

    public function totalErrored()
    {
        return $this->db->get_count('
          SELECT COUNT(`addressID`) 
          FROM ' . $this->table . ' 
          WHERE `addressError` IS NOT NULL;'
        );
    }

    public function create($data)
    {
        $data['addressUpdated'] = date('Y-m-d H:i:s');

        return parent::create($data);
    }

    public function getCustom(array $options)
    {

    }
}
