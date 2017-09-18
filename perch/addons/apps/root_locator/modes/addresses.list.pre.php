<?php

$Paging = $API->get('Paging');
$Paging->set_per_page('20');

$Addresses = new RootLocator_Addresses($API);

$addresses = [];
$filtered = false;

if (PerchRequest::get('chars')) {
    $filtered = true;
    $addresses = $Addresses->filterByTitleChar(PerchRequest::get('chars'), $Paging);
}

if (PerchRequest::get('filter') == 'complete') {
    $filtered = true;
    $addresses = $Addresses->filterByCoordinates($Paging);
}

if (PerchRequest::get('filter') == 'failed') {
    $filtered = true;
    $addresses = $Addresses->filterByErrors($Paging);
}

if (!$filtered) {
    $addresses = $Addresses->all($Paging);
}

if (!$Settings->get('root_locator_google_api_key')->val()) {
    $Alert->set('error', $Lang->get('There is no Google API key set. Please add your key Perch Settings.'));
}

if ($addresses === false) {
    $Addresses->attempt_install();
}

if ($Settings->get('root_locator_update')->val() != '2.0.0') {
    PerchUtil::redirect($API->app_path() . '/update/');
}
