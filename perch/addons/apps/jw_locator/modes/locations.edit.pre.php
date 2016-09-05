<?php

$Locations = new JwLocator_Locations($API);

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

    if($Location->is_queued()) {
        $Alert->set('notice', $Lang->get('The location is in the queue to be plotted onto the map.'));
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
            $Alert->set('error', $Lang->get('Sorry, that location could not be updated.'));
        }
    }

    if ($result) {
        $Alert->set('success', $Lang->get('The location has been successfully updated. Return to %slocations list%s', '<a href="' . $API->app_path() . '">', '</a>'));
    }
}

if (isset($_GET['created']) && !$message) {
    $Alert->set('success', $Lang->get('The location has been successfully updated. Return to %slocations list%s', '<a href="' . $API->app_path() . '">', '</a>'));
}

if(is_object($Location) && ($Location->is_failed())) {
    $Alert->set('error', $Lang->get('There was an issue plotting this location onto the map. <a href="%s/errors/?view=%s" class="action">View Details</a>', $API->app_path(), $HTML->encode($Location->id())));
}