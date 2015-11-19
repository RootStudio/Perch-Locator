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

