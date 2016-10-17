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
    public $static_fields = array(
        'addressTitle',
        'addressBuilding',
        'addressStreet',
        'addressTown',
        'addressRegion',
        'addressPostcode',
        'addressCountry'
    );

    public function allGeocoded($Paging = false)
    {

    }

    public function allErrored($Paging = false)
    {

    }

    public function totalGeocoded()
    {

    }

    public function totalErrored()
    {

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
