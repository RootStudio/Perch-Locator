<?php

$Paging = $API->get('Paging');
$Paging->set_per_page('20');

$Addresses = new RootLocator_Addresses($API);

$addresses = [];
$filtered = false;

if(isset($_GET['chars']) && $_GET['chars'] != '') {
    $filtered = true;
    $addresses = $Addresses->filterByTitleChar($_GET['chars'], $Paging);
}

if(isset($_GET['filter']) && $_GET['filter'] == 'complete') {
    $filtered = true;
    $addresses = $Addresses->filterByCoordinates($Paging);
}

if(isset($_GET['filter']) && $_GET['filter'] == 'failed') {
    $filtered = true;
    $addresses = $Addresses->filterByErrors($Paging);
}

if(!$filtered) {
    $addresses = $Addresses->all($Paging);
}

if(!$Settings->get('root_locator_google_api_key')->val()) {
    $Alert->set('error', $Lang->get('There is no Google API key set. Please add your key Perch Settings.'));
}

if($addresses === false) {
    $Addresses->attempt_install();
}
