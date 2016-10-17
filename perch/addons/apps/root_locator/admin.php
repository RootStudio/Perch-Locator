<?php

if ($CurrentUser->logged_in() && $CurrentUser->has_priv('root_locator')) {
    $this->register_app('root_locator', 'Locator', 1, 'Provide location based listings for your site', '2.0.0');
    $this->require_version('root_locator', '2.8.31');
    $this->add_create_page('root_locator', 'edit');

    $this->add_setting('root_locator_batch_size', 'Batch Size', 'select', 25, array(
        array('label' => '10 Locations', 'value' => 10),
        array('label' => '25 Locations', 'value' => 25),
        array('label' => '50 Locations', 'value' => 50),
        array('label' => '100 Locations', 'value' => 100)
    ));

    $this->add_setting('root_locator_google_api_key', 'API key', 'text');

    include(__DIR__ . '/lib/vendor/autoload.php');

    spl_autoload_register(function ($class_name) {
        if (strpos($class_name, 'RootLocator') === 0) {
            include(PERCH_PATH . '/addons/apps/root_locator/lib/' . $class_name . '.class.php');

            return true;
        }

        return false;
    });
}
