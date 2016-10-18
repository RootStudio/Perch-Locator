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

    public function getQueued(array $ids)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE ' . $this->pk . ' IN(' . implode(',', $ids) . ');';
        $rows = $this->db->get_rows($sql);

        return $this->return_instances($rows);
    }

    /**
     * Return results filtered by first character of title column
     *
     * @param string     $string
     * @param bool $Paging
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

    public function create($data)
    {
        $data['addressUpdated'] = date('Y-m-d H:i:s');

        return parent::create($data);
    }

    public function getCustom(array $options)
    {

    }
}
