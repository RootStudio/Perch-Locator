<?php

class JwStockists_Markers extends PerchAPI_Factory
{
    protected $table = 'jw_stockists_markers';

    protected $pk = 'markerID';

    protected $default_sort_column = 'markerID';

    protected $default_sort_direction = 'DESC';

    protected $singular_classname = 'JwStockists_Marker';

    protected $namespace = 'locator';

    public $static_fields = array(
        'markerID',
        'markerLatitude',
        'markerLongitude'
    );
}
