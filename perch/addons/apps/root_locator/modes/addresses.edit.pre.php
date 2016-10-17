<?php

// Data
$Addresses = new RootLocator_Addresses($API);
$result = false;

// Master template
$Template->set('locator/address.html', 'locator');

// Edit / Create
if(isset($_GET['id']) && $_GET['id'] != '') {

    $addressID = (int) $_GET['id'];
    $Address = $Addresses->find($addressID);
    $details = $Address->to_array();

    $heading1 = 'Addresses / Edit Address';

} else {

    $addressID = false;
    $Address = false;
    $details = [];

    $heading1 = 'Locations / Add Address';
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
        $result = $Address->update($data, $force);
        $details = $Address->to_array();
    } else {
        $new_address = $Addresses->create($data);

        if($new_address) {
            if($force) {
                $new_address->geocode();
            }

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
