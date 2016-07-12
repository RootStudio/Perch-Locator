<?php

if ($CurrentUser->logged_in() && $CurrentUser->has_priv('jw_locator')) {
    $this->register_app('jw_locator', 'Locator', 1, 'Store / Location finder', '1.1.0');
    $this->require_version('jw_locator', 2.8);
    $this->add_create_page('jw_locator', 'edit');

    $this->add_setting('jw_locator_batch_size', 'Batch Size', 'select', 25, array(
        array('label' => '10 Locations', 'value' => 10),
        array('label' => '25 Locations', 'value' => 25),
        array('label' => '50 Locations', 'value' => 50),
        array('label' => '100 Locations', 'value' => 100)
    ));

    $this->add_setting('jw_locator_google_api_key', 'Google API Key', 'text');

    spl_autoload_register(function ($class_name) {
        if (strpos($class_name, 'JwLocator') === 0) {
            include(PERCH_PATH . '/addons/apps/jw_locator/classes/' . $class_name . '.class.php');

            return true;
        }

        return false;
    });
}
