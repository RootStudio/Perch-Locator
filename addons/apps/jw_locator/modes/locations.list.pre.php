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
    $Alert->set('notice', $Lang->get('No locations have been added to the system.'));
}

if($Errors->total() > 0) {
    $Alert->set('error', $Lang->get('There were issues plotting some locations on the map. <a href="%s/errors/" class="action">View Details</a>', $HTML->encode($API->app_path())));
}