<?php

$Locations = new JwStockists_Locations($API);

// Load APIs
$HTML = $API->get('HTML');
$Form = $API->get('Form');
$Text = $API->get('Text');
$Template = $API->get('Template');

// Master Template
$Template->set('locator/location.html', 'locator');

// Defaults
$result = false;
$message = false;

// Edit / Create
if (isset($_GET['id']) && $_GET['id'] != '') {
    $locationID = (int)$_GET['id'];
    $Location = $Locations->find($locationID);
    $details = $Location->to_array();

    $heading1 = 'Locations / Edit Location';

    if($Location->get_status() === 1) {
        $message = $HTML->warning_message('This location is currently queued for Geocoding');
    }
} else {
    $locationID = false;
    $Location = false;
    $details = array();

    $heading1 = 'Locations / Add Location';
}

// Form Handling
$Form->handle_empty_block_generation($Template);

$Form->require_field('locationTitle', 'Required');
$Form->require_field('locationBuilding', 'Required');
$Form->require_field('locationStreet', 'Required');
$Form->require_field('locationTown', 'Required');
$Form->require_field('locationCountry', 'Required');
$Form->require_field('locationPostcode', 'Required');

$Form->set_required_fields_from_template($Template, $details);

if ($Form->submitted()) {
    $postvars = array(
        'locationTitle',
        'locationBuilding',
        'locationStreet',
        'locationTown',
        'locationRegion',
        'locationCountry',
        'locationPostcode',
        'force_geocoding'
    );

    $data = $Form->receive($postvars);

    // Force update
    $force_geocoding = isset($data['force_geocoding']);
    unset($data['force_geocoding']);

    // Dynamic data
    $previous_values = false;

    if (isset($details['locationDynamicFields'])) {
        $previous_values = PerchUtil::json_safe_decode($details['locationDynamicFields'], true);
    }

    $dynamic_fields = $Form->receive_from_template_fields($Template, $previous_values, $Locations, $Location);
    $data['locationDynamicFields'] = PerchUtil::json_safe_encode($dynamic_fields);

    // Store
    if (is_object($Location)) {
        $result = $Location->update($data, $force_geocoding);
        $details = $Location->to_array();
    } else {
        if (isset($data['locationID'])) {
            unset($data['locationID']);
        }

        $new_location = $Locations->create($data);

        if ($new_location) {
            PerchUtil::redirect($API->app_path() . '/edit/?id=' . $new_location->id() . '&created=1');
        } else {
            $message = $HTML->failure_message('Sorry, that location could not be updated.');
        }
    }

    if ($result) {
        $message = $HTML->success_message('The location has been successfully updated. Return to %slocations list%s',
            '<a href="' . $API->app_path() . '">', '</a>');
    }
}

if (isset($_GET['created']) && !$message) {
    $message = $HTML->success_message('The location has been successfully updated. Return to %slocations list%s',
        '<a href="' . $API->app_path() . '">', '</a>');
}

if(is_object($Location) && ($Location->get_status() === 4)) {
    $message = $HTML->failure_message('This location could not be Geocoded <a href="'. $HTML->encode($API->app_path()) .'/errors/?view='. $HTML->encode(urlencode($Location->id())) .'" class="action">View Details</a>');
}