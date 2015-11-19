<?php

include 'classes/JwLocator_QueryCache.class.php';
include 'classes/JwLocator_Locations.class.php';
include 'classes/JwLocator_Location.class.php';
include 'classes/JwLocator_Geocode.class.php';
include 'classes/JwLocator_Markers.class.php';
include 'classes/JwLocator_Marker.class.php';
include 'classes/JwLocator_Errors.class.php';
include 'classes/JwLocator_Error.class.php';

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

function jw_locator_location_json(array $locations, $return = false)
{
    $json = PerchUtil::json_safe_encode($locations, true);

    if ($return) {
        return $json;
    }

    echo $json;
}

