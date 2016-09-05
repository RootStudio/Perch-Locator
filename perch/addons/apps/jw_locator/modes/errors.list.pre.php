<?php

if (!$CurrentUser->has_priv('jw_locator.errors')) {
    PerchUtil::redirect($API->app_path());
}

$HTML = $API->get('HTML');

$Paging = $API->get('Paging');
$Paging->set_per_page('20');

$Errors = new JwLocator_Errors($API);
$Locations = new JwLocator_Locations($API);

$locations = array();

if(isset($_GET['view']) && $_GET['view'] != "") {
    $location_id = (int) $_GET['view'];
    $locations = $Locations->all_in_array(array($location_id));
} else {
    $locations = $Errors->get_locations($Paging);
}

if ($locations === false) {
    $Alert->set('filter', $Lang->get('All locations have been plotted successfully.'));
}