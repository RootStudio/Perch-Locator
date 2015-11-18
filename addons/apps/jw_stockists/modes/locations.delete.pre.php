<?php

$Locations = new JwStockists_Locations($API);

$HTML = $API->get('HTML');
$Form = $API->get('Form');
$Form->set_name('delete');

$message = false;

//check that we have the ID of a thing, if not redirect back to the listing.
if (isset($_GET['id']) && $_GET['id']!='') {
    $Location = $Locations->find($_GET['id'], true);
}else{
    PerchUtil::redirect($API->app_path());
}

// if the confirmation of delete has been submitted this is a form post
if ($Form->submitted()) {

    if (is_object($Location)) {
        $Location->delete();

        if ($Form->submitted_via_ajax) {
            echo $API->app_path().'/';
            exit;
        }
        else{
            PerchUtil::redirect($API->app_path().'/');
        }

    }
    else{
        $message = $HTML->failure_message('Sorry, that location could not be deleted.');
    }
}

$details = $Location->to_array();