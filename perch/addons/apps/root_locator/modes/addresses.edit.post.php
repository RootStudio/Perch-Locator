<?php

// Side Panel UI
echo $HTML->side_panel_start();
echo $HTML->para('This page allows you to add / edit a location in the system.');
echo $HTML->para('To prevent performance issues, all updates are queued to be plotted onto the map. Changes can take up to 10 minutes to appear.');
echo $HTML->para('If changes need to be made immediately, ticking the "force geocoding" box will update the map on save.');
echo $HTML->side_panel_end();

// Main Panel UI
echo $HTML->main_panel_start();

echo $HTML->heading1($heading1);

// Output alerts
$Alert->output();

// Begin form
echo $HTML->heading2('Address');

echo $Form->form_start();

    echo $Form->text_field('addressTitle', 'Title', isset($details['addressTitle']) ? $details['addressTitle'] : false, 'xl');
    echo $Form->text_field('addressBuilding', 'Building', isset($details['addressBuilding']) ? $details['addressBuilding'] : false, 'm');
    echo $Form->textarea_field('addressStreet', 'Street', isset($details['addressStreet']) ? $details['addressStreet'] : false, 'xs');
    echo $Form->text_field('addressTown', 'Town / City', isset($details['addressTown']) ? $details['addressTown'] : false, 'l');
    echo $Form->text_field('addressRegion', 'Region', isset($details['addressRegion']) ? $details['addressRegion'] : false, 'l');
    echo $Form->text_field('addressPostcode', 'Postcode', isset($details['addressPostcode']) ? $details['addressPostcode'] : false, 'm');
    echo $Form->text_field('addressCountry', 'Country', isset($details['addressCountry']) ? $details['addressCountry'] : false, 'm');

    echo $HTML->heading2('Map');

    if(is_object($Address) && !$Address->hasCoordinates()) {
        echo $HTML->warning_message('The address is in the queue to be plotted on the map.');
    }

    if(is_object($Address) && $Address->hasError()) {
        echo $HTML->failure_message(RootLocator_Errors::get($Address->addressError()));
    }

    if(is_object($Address) && $Address->preview()) {
        echo $Address->preview();
    }

    echo $Form->hint('Tick this to force the map to update immediately.');
    echo $Form->checkbox_field('force', 'Force update map', '1');

    echo $Form->fields_from_template($Template, $details, $Addresses->static_fields);

    echo $Form->submit_field('btnSubmit', 'Save', $API->app_path());

echo $Form->form_end();

// Main Panel UI
echo $HTML->main_panel_end();
