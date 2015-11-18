<?php

include PERCH_PATH . '/addons/apps/jw_stockists/classes/JwStockists_Locations.class.php';
include PERCH_PATH . '/addons/apps/jw_stockists/classes/JwStockists_Location.class.php';
include PERCH_PATH . '/addons/apps/jw_stockists/classes/JwStockists_Markers.class.php';
include PERCH_PATH . '/addons/apps/jw_stockists/classes/JwStockists_Marker.class.php';
include PERCH_PATH . '/addons/apps/jw_stockists/classes/JwStockists_Errors.class.php';
include PERCH_PATH . '/addons/apps/jw_stockists/classes/JwStockists_Error.class.php';

function jw_stockists_marker_json($opts = array()) {
    $API = new PerchAPI(1.0, 'jw_stockists');
    $Markers = new JwStockists_Markers($API);

    $locations = $Markers->get_locations();

    return PerchUtil::json_safe_encode($locations);
}
