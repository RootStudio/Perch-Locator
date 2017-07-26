<?php

if ($CurrentUser->logged_in() && $CurrentUser->has_priv('root_locator')) {
    $this->register_app('root_locator', 'Locator', 1, 'Provide location based listings for your site', '3.0.0');
    $this->require_version('root_locator', '3.0.3');
    $this->add_create_page('root_locator', 'edit');

    $this->add_setting('root_locator_batch_size', 'Batch Size', 'select', 25, array(
        array('label' => '10 Locations', 'value' => 10),
        array('label' => '25 Locations', 'value' => 25),
        array('label' => '50 Locations', 'value' => 50),
        array('label' => '100 Locations', 'value' => 100)
    ));

    $this->add_setting('root_locator_google_api_key', 'API key', 'text');

    require __DIR__ . '/autoloader.php';

    // Search handler
    PerchSystem::register_admin_search_handler('RootLocator_SearchHandler');
}
