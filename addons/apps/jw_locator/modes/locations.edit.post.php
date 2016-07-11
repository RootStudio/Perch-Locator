<?php

// Side Panel UI
echo $HTML->side_panel_start();
echo $HTML->para('This page allows you to add / edit a location in the system.');
echo $HTML->para('To prevent performance issues, all updates are queued to be plotted onto the map. Changes can take up to 10 minutes to appear.');
echo $HTML->para('If changes need to be made immediately, ticking the "force geocoding" box will update the map on save.');
echo $HTML->side_panel_end();

// Main Panel UI
echo $HTML->main_panel_start();
include('_subnav.php');

echo $HTML->heading1($heading1);

$Alert->output();

echo $HTML->heading2('Location details');

// Help
$template_help_html = $Template->find_help();
if ($template_help_html) {
    echo $HTML->heading2('Help');
    echo '<div id="template-help">' . $template_help_html . '</div>';
}

echo $Form->form_start();

    echo $Form->text_field('locationTitle', 'Title', isset($details['locationTitle']) ? $details['locationTitle'] : false, 'xl');

    echo $HTML->heading2('Address');
    echo $Form->text_field('locationBuilding', 'Building', isset($details['locationBuilding']) ? $details['locationBuilding'] : false, 'm');
    echo $Form->text_field('locationStreet', 'Street', isset($details['locationStreet']) ? $details['locationStreet'] : false, 'l');
    echo $Form->text_field('locationTown', 'Town / City', isset($details['locationTown']) ? $details['locationTown'] : false, 'm');
    echo $Form->text_field('locationRegion', 'Region', isset($details['locationRegion']) ? $details['locationRegion'] : false, 'm');
    echo $Form->text_field('locationPostcode', 'Postcode', isset($details['locationPostcode']) ? $details['locationPostcode'] : false, 's');

    $country_opts = include '../utilities/country_codes.php';
    echo $Form->select_field('locationCountry', 'Country', $country_opts, isset($details['locationCountry']) ? $details['locationCountry'] : 'GB');

    if(is_object($Location)) {
        echo $Form->checkbox_field('force_geocoding', 'Force Geocoding', '1');
    }

    if(is_object($Location) && ($Location->is_synced())) {
        echo $HTML->heading2('Preview');
        echo $Location->map_preview();
    }

    echo $Form->fields_from_template($Template, $details, $Locations->static_fields);
    echo $Form->submit_field('btnSubmit', 'Save', $API->app_path());

echo $Form->form_end();
echo $HTML->main_panel_end();