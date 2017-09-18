<?php

// Title

if (is_object($Address)) {
    $heading = $Lang->get('Editing ‘%s’', $HTML->encode($Address->addressTitle()));
} else {
    $heading = $Lang->get('Creating a New Address');
}

echo $HTML->title_panel([
    'heading' => $Lang->get($heading)
], $CurrentUser);

// Output alerts
$Alert->output();

// Begin form
echo $HTML->heading2('Address');

echo $Form->form_start();

    echo $Form->text_field('addressTitle', 'Title', isset($details['addressTitle']) ? $details['addressTitle'] : false, 'input-simple xl');
    echo $Form->text_field('addressBuilding', 'Building', isset($details['addressBuilding']) ? $details['addressBuilding'] : false, 'input-simple m');
    echo $Form->textarea_field('addressStreet', 'Street', isset($details['addressStreet']) ? $details['addressStreet'] : false, 'input-simple s');
    echo $Form->text_field('addressTown', 'Town / City', isset($details['addressTown']) ? $details['addressTown'] : false, 'input-simple l');
    echo $Form->text_field('addressRegion', 'Region', isset($details['addressRegion']) ? $details['addressRegion'] : false, 'input-simple l');
    echo $Form->text_field('addressPostcode', 'Postcode', isset($details['addressPostcode']) ? $details['addressPostcode'] : false, 'input-simple m');
    echo $Form->text_field('addressCountry', 'Country', isset($details['addressCountry']) ? $details['addressCountry'] : false, 'input-simple m');

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
