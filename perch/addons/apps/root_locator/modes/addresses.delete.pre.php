<?php

$Addresses = new RootLocator_Addresses($API);

if (PerchRequest::get('id')) {
    $Address = $Addresses->find(PerchRequest::get('id'), true);
} else {
    PerchUtil::redirect($API->app_path());
}

$Form->set_name('delete');

if ($Form->submitted()) {
    if (is_object($Address)) {

        $Address->delete();

        if ($Form->submitted_via_ajax) {
            echo $API->app_path() . '/';
            exit;
        } else {
            PerchUtil::redirect($API->app_path() . '/');
        }
    } else {
        $Alert->set('error', $Lang->get('Sorry, the address could not be deleted.'));
    }
}

$details = $Address->to_array();
