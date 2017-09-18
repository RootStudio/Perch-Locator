<?php

include(__DIR__ . '/lib/vendor/autoload.php');

spl_autoload_register(function ($class_name) {
    if (strpos($class_name, 'API_RootLocator') > 0) {
        include(PERCH_PATH . '/addons/apps/root_locator/lib/api/' . $class_name . '.class.php');

        return true;
    }

    if (strpos($class_name, 'RootLocator') === 0) {
        include(PERCH_PATH . '/addons/apps/root_locator/lib/' . $class_name . '.class.php');

        return true;
    }

    return false;
});
