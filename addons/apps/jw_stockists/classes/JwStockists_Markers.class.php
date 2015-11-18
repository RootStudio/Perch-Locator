<?php

/**
 * Class JwStockists_Markers
 *
 * @author James Wigger <james.s.wigger@gmail.com>
 */
class JwStockists_Markers extends PerchAPI_Factory
{
    /**
     * Markers table
     *
     * @var string
     */
    protected $table = 'jw_stockists_markers';

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
    protected $singular_classname = 'JwStockists_Marker';

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
}
