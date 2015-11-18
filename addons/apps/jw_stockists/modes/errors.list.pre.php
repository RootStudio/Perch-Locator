<?php

$HTML = $API->get('HTML');

$Paging = $API->get('Paging');
$Paging->set_per_page('20');

$Errors = new JwStockists_Errors($API);

$locations = array();
$message = false;

$locations = $Errors->get_locations($Paging);

if ($locations === false) {
    $message = $HTML->success_message('There have been no failed Geocoding jobs');
}
