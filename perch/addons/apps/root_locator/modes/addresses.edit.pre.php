<?php

// Data
$Addresses = new RootLocator_Addresses($API);
$Tasks = new RootLocator_Tasks($API);
$result = false;

// Master template
$Template->set('locator/address.html', 'locator');

// Edit / Create
if(PerchRequest::get('id')) {

    $addressID = (int) PerchRequest::get('id');
    $Address = $Addresses->find($addressID);
    $details = $Address->to_array();

} else {

    $addressID = false;
    $Address = false;
    $details = [];
}

// Forms
$Form->handle_empty_block_generation($Template);
$Form->require_field('addressTitle', 'This field is required');
$Form->require_field('addressStreet', 'This field is required');
$Form->require_field('addressPostcode', 'This field is required');
$Form->set_required_fields_from_template($Template, $details);

if($Form->submitted()) {

    $postvars = [
        'addressTitle',
        'addressBuilding',
        'addressStreet',
        'addressTown',
        'addressRegion',
        'addressCountry',
        'addressPostcode',
        'force'
    ];

    $data = $Form->receive($postvars);

    // Force?
    $force = false;
    if(isset($data['force'])) {
        $force = true;
        unset($data['force']);
    }

    // Dynamic fields
    $previous_values = false;
    if(isset($details['addressDynamicFields'])) {
        $previous_values = PerchUtil::json_safe_decode($details['addressDynamicFields'], true);
    }

    $dynamic_fields = $Form->receive_from_template_fields($Template, $previous_values, $Addresses, $Address);
    $data['addressDynamicFields'] = PerchUtil::json_safe_encode($dynamic_fields);

    // Save
    if(is_object($Address)) {
        $requeue = $Address->shouldQueue($data);

        if(!$force && $requeue) {
            $Tasks->add('address.geocode', $Address->id());

            $data['addressLatitude'] = null;
            $data['addressLongitude'] = null;
        }

        $result = $Address->update($data, $force);
        $details = $Address->to_array();
        $Address->index($Template);
    } else {
        $new_address = $Addresses->create($data);

        if($new_address) {
            if($force) {
                $new_address->geocode();
            } else {
                $Tasks->add('address.geocode', $new_address->id());
            }

            $new_address->index($Template);

            PerchUtil::redirect($API->app_path() . '/edit/?id=' . $new_address->id() . '&created=1');
        } else {
            $Alert->set('error', $Lang->get('Sorry, that location could not be created.'));
        }
    }

    if ($result) {
        $Alert->set('success', $Lang->get('The address has been successfully updated. Return to %saddress list%s.', '<a href="' . $API->app_path() . '">', '</a>'));
    }
}

if (isset($_GET['created']) && !$result) {
    $Alert->set('success', $Lang->get('The address has been created. Return to %saddress list%s.', '<a href="' . $API->app_path() . '">', '</a>'));
}
