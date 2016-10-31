<?php

include(__DIR__ . '/lib/vendor/autoload.php');

spl_autoload_register(function ($class_name) {
    if (strpos($class_name, 'RootLocator') === 0) {
        include(PERCH_PATH . '/addons/apps/root_locator/lib/' . $class_name . '.class.php');

        return true;
    }

    return false;
});

// Search Handler
PerchSystem::register_search_handler('RootLocator_SearchHandler');

/**
 * Shortcut to output detail for single address
 *
 * @param int     $id
 * @param bool $return
 *
 * @return void|array
 */
function root_locator_address($id, $return = false)
{
    $options = [
        'template' => 'address.html',
        '_id' => (int) $id
    ];

    if($return) {
        return root_locator_get_custom($options, true);
    }

    root_locator_get_custom($options);
}

/**
 * Output data from single field
 *
 * @param      $id
 * @param      $field
 * @param bool $return
 *
 * @return bool|string
 */
function root_locator_address_field($id, $field, $return = false)
{
    $API = new PerchAPI(1.0, 'root_locator');
    $Addresses = new RootLocator_Addresses($API);

    $Address = $Addresses->find((int) $id);
    $result = false;

    if(is_object($Address)) {
        $result = $Address->getField($field);
    }

    if($return) {
        return $result;
    }

    echo $result;
}

/**
 * Display list of addresses near to location specified by ID
 *
 * @param       $id
 * @param array $opts
 * @param bool  $return
 *
 * @return void|array
 */
function root_locator_nearby($id, array $opts = [], $return = false)
{
    $API = new PerchAPI(1.0, 'root_locator');
    $Addresses = new RootLocator_Addresses($API);

    $Address = $Addresses->find((int) $id);
    $coordinates = false;

    if(is_object($Address)) {
        $coordinates = $Address->getCoordinates();
    }

    if($coordinates) {
        if(isset($opts['address'])) $opts['address'] = null;

        $opts['coordinates'] = $coordinates;
        $opts['exclude'] = $id;

        if($return) {
            root_locator_get_custom($opts, $return);
        }

        root_locator_get_custom($opts, $return);
    }
}

/**
 * Perform custom queries on address data and output results
 *
 * @param array $opts
 * @param bool  $return
 *
 * @return void|string
 */
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
