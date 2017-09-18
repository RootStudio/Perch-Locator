<?php

if ($CurrentUser->logged_in() && $CurrentUser->has_priv('root_locator')) {
    $this->register_app('root_locator', 'Locator', 1, 'Provide location based listings for your site', '3.0.0');
    $this->require_version('root_locator', '3.0.3');
    $this->add_create_page('root_locator', 'edit');

    require __DIR__ . '/_autoload.php';

    require __DIR__ . '/_settings.php';

    require __DIR__ . '/_fieldtypes.php';

    // Search handler
    PerchSystem::register_admin_search_handler('RootLocator_SearchHandler');
}
