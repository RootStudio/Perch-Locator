<?php

$HTML = $API->get('HTML');

$Paging = $API->get('Paging');
$Paging->set_per_page('20');

$Locations = new JwLocator_Locations($API);
$Errors = new JwLocator_Errors($API);

$locations = array();
$message = false;

$locations = $Locations->all($Paging);

if ($locations === false) {
    $Locations->attempt_install();
    $message = $HTML->warning_message('No locations have been added to the system.');
}

if($Errors->total() > 0) {
    $message = $HTML->failure_message('There were issues plotting some locations on the map. <a href="'. $HTML->encode($API->app_path()) .'/errors/" class="action">View Details</a>');
}