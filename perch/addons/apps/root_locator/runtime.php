<?php

include(__DIR__ . '/lib/vendor/autoload.php');

spl_autoload_register(function ($class_name) {
    if (strpos($class_name, 'RootLocator') === 0) {
        include(PERCH_PATH . '/addons/apps/root_locator/lib/' . $class_name . '.class.php');

        return true;
    }

    return false;
});

function root_locator_address($id, array $opts = [], $return = false)
{

}

function root_locator_address_fields($id, ...$fields)
{

}

function root_locator_nearby($id, array $opts = [], $return = false)
{

}

function root_locator_get_custom(array $opts = [], $return = false)
{
    $defaults = [
        'address'       => null,
        'coordinates'   => null,
        'range'         => 25,
        'skip-template' => false,
        'split-items'   => false,
        'filter'        => false,
        'paginate'      => false,
        'template'      => 'address_list.html'
    ];

    $opts = array_merge($defaults, $opts);

    if ($opts['skip-template'] || $opts['split-items']) {
        $return = true;
    }

    $API = new PerchAPI(1.0, 'root_locator');
    $Addresses = new RootLocator_Addresses($API);

    $result = $Addresses->getCustom($opts);

    if($return) {
        return $result;
    }

    echo $result;
}
