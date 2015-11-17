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
}
