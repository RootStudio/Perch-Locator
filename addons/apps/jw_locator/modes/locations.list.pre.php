<?php

$HTML = $API->get('HTML');

$Paging = $API->get('Paging');
$Paging->set_per_page('20');

$Locations = new JwLocator_Locations($API);

$locations = array();
$message = false;

$locations = $Locations->all($Paging);

if ($locations === false) {
    $Locations->attempt_install();
    $message = $HTML->warning_message('There are no locations stored in the system.');
}
