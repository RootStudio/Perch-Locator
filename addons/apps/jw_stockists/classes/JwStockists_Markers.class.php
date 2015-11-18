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

    public function get_locations()
    {
        $markers = $this->all();

        $marker_ids = array();
        if(PerchUtil::count($markers)) {
            foreach($markers as $Marker) {
                $marker_ids[] = (int) $Marker->id();
            }
        }

        $Locations = new JwStockists_Locations($this->api);
        $locations = $Locations->all_with_markers($marker_ids);

        if(PerchUtil::count($locations)) {
            foreach($locations as $Location) {
                $Marker = array_filter($markers, function($Marker) use($location_id) {
                    $Marker->id() == $location_id;
                });

                $Location->squirrel('markerLatitude', $Marker->markerLatitude());
                $Location->squirrel('markerLongitude', $Marker->markerLongitude());
            }
        }
        
        return $Locations;
    }
}
