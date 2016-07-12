<?php

$HTML = $API->get('HTML');
$Settings = $API->get('Settings');
$Paging = $API->get('Paging');
$Paging->set_per_page('20');

$Locations = new JwLocator_Locations($API);
$Errors = new JwLocator_Errors($API);

$locations = array();
$message = false;


if(isset($_GET['chars']) && $_GET['chars'] != '') {
    $locations = $Locations->all_by_start_character($_GET['chars'], $Paging);

    if ($locations === false) {
        $Alert->set('notice', $Lang->get('No locations could be found for the selected filter.'));
    }
} else {
    $locations = $Locations->all($Paging);

    if ($locations === false) {
        $Locations->attempt_install();
        $Alert->set('notice', $Lang->get('No locations have been added to the system.'));
    }
}

if($Errors->total() > 0) {
    $Alert->set('error', $Lang->get('There were issues plotting some locations on the map. <a href="%s/errors/" class="action">View Details</a>', $HTML->encode($API->app_path())));
}

if(!$Settings->get('jw_locator_google_api_key')->val()) {
    return $Alert->set('notice', $Lang->get('There is no Google API key set. Please add your key using the Perch settings area.'));
}