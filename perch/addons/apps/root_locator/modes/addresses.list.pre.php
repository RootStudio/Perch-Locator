<?php

$Paging = $API->get('Paging');
$Paging->set_per_page('20');

$Addresses = new RootLocator_Addresses($API);

$addresses = [];
$addresses = $Addresses->all($Paging);

if(!$Settings->get('root_locator_google_api_key')->val()) {
    $Alert->set('error', $Lang->get('There is no Google API key set. Please add your key Perch Settings.'));
}

if($addresses === false) {
    $Addresses->attempt_install();
    $Alert->set('notice', $Lang->get('There are no addresses saved - use the "Add Addresses" button to begin.'));
}
