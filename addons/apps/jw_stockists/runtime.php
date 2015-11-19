<?php

include PERCH_PATH . '/addons/apps/jw_stockists/classes/JwStockists_Locations.class.php';
include PERCH_PATH . '/addons/apps/jw_stockists/classes/JwStockists_Location.class.php';
include PERCH_PATH . '/addons/apps/jw_stockists/classes/JwStockists_Geocode.class.php';
include PERCH_PATH . '/addons/apps/jw_stockists/classes/JwStockists_Markers.class.php';
include PERCH_PATH . '/addons/apps/jw_stockists/classes/JwStockists_Marker.class.php';
include PERCH_PATH . '/addons/apps/jw_stockists/classes/JwStockists_Errors.class.php';
include PERCH_PATH . '/addons/apps/jw_stockists/classes/JwStockists_Error.class.php';

function jw_stockists_marker_json($options = array(), $return = false)
{
    $opts = array_merge(array(
        'address' => null,
        'radius'  => 50,
        'count'   => 25
    ), $options);

    $API = new PerchAPI(1.0, 'jw_stockists');
    $Markers = new JwStockists_Markers($API);

    if(!is_null($opts['address']) && $opts['address']) {
        $locations = $Markers->get_nearest_locations($opts['address'], $opts['radius'], $opts['count']);
    } else {
        $locations = $Markers->get_locations();
    }

    if ($return) {
        return PerchUtil::json_safe_encode($locations);
    }

    echo PerchUtil::json_safe_encode($locations, true);
}

function jw_locator_get_nearest($address, array $options = array())
{

}

function jw_locator_get_custom(array $opts, $return = false)
{
    $defaults = array(
        'address'       => null,
        'radius'        => 25,
        'skip-template' => false,
        'split-items'   => false,
        'filter'        => false,
        'paginate'      => false,
        'template'      => 'location_in_list.html',
    );

    $opts = array_merge($defaults, $opts);

    if ($opts['skip-template'] || $opts['split-items']) {
        $return = true;
    }

    $API = new PerchAPI(1.0, 'jw_stockists');
    $Locations = new JwStockists_Locations($API);

    $result = $Locations->get_custom($opts);

    if ($return) {
        return $result;
    }

    echo $result;
}

function jw_locator_location()
{

}

function jw_locator_location_json(array $locations)
{

}

