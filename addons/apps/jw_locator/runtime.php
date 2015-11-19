<?php

include PERCH_PATH . '/addons/apps/jw_locator/classes/JwLocator_Locations.class.php';
include PERCH_PATH . '/addons/apps/jw_locator/classes/JwLocator_Location.class.php';
include PERCH_PATH . '/addons/apps/jw_locator/classes/JwLocator_Geocode.class.php';
include PERCH_PATH . '/addons/apps/jw_locator/classes/JwLocator_Markers.class.php';
include PERCH_PATH . '/addons/apps/jw_locator/classes/JwLocator_Marker.class.php';
include PERCH_PATH . '/addons/apps/jw_locator/classes/JwLocator_Errors.class.php';
include PERCH_PATH . '/addons/apps/jw_locator/classes/JwLocator_Error.class.php';

function jw_locator_marker_json($options = array(), $return = false)
{
    $opts = array_merge(array(
        'address' => null,
        'radius'  => 50,
        'count'   => 25
    ), $options);

    $API = new PerchAPI(1.0, 'jw_locator');
    $Markers = new JwLocator_Markers($API);

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

function jw_locator_get_custom(array $opts, $return = false)
{
    $defaults = array(
        'address'       => null,
        'radius'        => 25,
        'skip-template' => false,
        'split-items'   => false,
        'filter'        => false,
        'paginate'      => false,
        'json'          => false,
        'template'      => 'location_in_list.html',
    );

    $opts = array_merge($defaults, $opts);

    if ($opts['skip-template'] || $opts['split-items']) {
        $return = true;
    }

    if($opts['json']) {
        $opts['skip-template'] = true;
    }

    $API = new PerchAPI(1.0, 'jw_locator');
    $Locations = new JwLocator_Locations($API);

    $result = $Locations->get_custom($opts);

    if($opts['json']) {
        $result = jw_locator_location_json($result, true);
    }

    if ($return) {
        return $result;
    }

    echo $result;
}

function jw_locator_location()
{

}

function jw_locator_location_json(array $locations, $return = false)
{
    $json = PerchUtil::json_safe_encode($locations, true);

    if ($return) {
        return $json;
    }

    echo $json;
}

