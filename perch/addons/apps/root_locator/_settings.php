<?php

$this->add_setting('root_locator_address_url', 'Address URL', 'text', '/locations/view.php?s={addressSlug}');
$this->add_setting('root_locator_address_slug', 'Slug format', 'text', '{addressTitle}-{addressPostcode}');

$this->add_setting('root_locator_batch_size', 'Batch Size', 'select', 25, array(
    array('label' => '10 Locations', 'value' => 10),
    array('label' => '25 Locations', 'value' => 25),
    array('label' => '50 Locations', 'value' => 50),
    array('label' => '100 Locations', 'value' => 100)
));

$this->add_setting('root_locator_google_api_key', 'API key', 'text');