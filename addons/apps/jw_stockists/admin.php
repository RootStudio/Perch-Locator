<?php

if($CurrentUser->logged_in() && $CurrentUser->has_priv('jw_stockists')) {
    $this->register_app('jw_stockists', 'Locator', 1, 'Store / Location finder', '1.0.0');
    $this->require_version('jw_stockists', 2.8);

    $this->add_setting('jw_stockists_batch_size', 'Batch Size', 'select', 25, array(
        array('label' => '10', 'value' => 10),
        array('label' => '25', 'value' => 25),
        array('label' => '50', 'value' => 50),
        array('label' => '100', 'value' => 100)
    ));

    $this->add_setting('jw_stockists_google_api_key', 'Google API Key', 'text');
}
