<?php

// Master template
$Template->set('locator/address.html', 'locator');

$Addresses = new RootLocator_Addresses($API);
$Tasks = new RootLocator_Tasks($API);

$Version = $Settings->get('root_locator_update')->val();
$Paging = false;

if (version_compare($Version, '2.0.0', '<')) {
    $Paging = $API->get('Paging');
    $Paging->set_per_page(10);

    if ($Paging->is_first_page()) {

        // Update permissions
        $UserPrivileges = $API->get('UserPrivileges');
        $UserPrivileges->create_privilege('root_locator', 'Access the locator app');
        $UserPrivileges->create_privilege('root_locator.import', 'Mass import location data');

        // Convert categories
        $sql = 'UPDATE ' . PERCH_DB_PREFIX . 'category_sets 
            SET setTemplate="~root_locator/templates/locator/category_set.html", setCatTemplate="~root_locator/templates/locator/category.html"
            WHERE setTemplate="~/jw_locator/templates/locator/category_set.html"
            AND setCatTemplate="~/jw_locator/templates/locator/category.html"';

        $db->execute($sql);
    }

    $legacy = $Addresses->getLegacyData($Paging);

    if (PerchUtil::count($legacy)) {
        foreach ($legacy as $row) {

            // Ok, we have an error and it's not a quota issue, so just save as is.
            if (isset($row['errorMessage']) && !empty($row['errorMessage']) && $row['errorMessage'] == 'The address could not be found.') {
                $legacyAddress = $Addresses->create([
                    'addressTitle'         => $row['locationTitle'],
                    'addressBuilding'      => $row['locationBuilding'],
                    'addressStreet'        => $row['locationStreet'],
                    'addressTown'          => $row['locationTown'],
                    'addressRegion'        => $row['locationRegion'],
                    'addressPostcode'      => $row['locationPostcode'],
                    'addressCountry'       => $row['locationPostcode'],
                    'addressDynamicFields' => $row['locationDynamicFields'],
                    'addressError'         => 'no_results'
                ]);

                $legacyAddress->index($Template);

                continue;
            }

            // Do we have some existing location data we can just simply shift over?
            if (isset($row['markerLatitude']) && isset($row['markerLongitude'])) {
                $legacyAddress = $Addresses->create([
                    'addressTitle'         => $row['locationTitle'],
                    'addressBuilding'      => $row['locationBuilding'],
                    'addressStreet'        => $row['locationStreet'],
                    'addressTown'          => $row['locationTown'],
                    'addressRegion'        => $row['locationRegion'],
                    'addressPostcode'      => $row['locationPostcode'],
                    'addressCountry'       => $row['locationPostcode'],
                    'addressDynamicFields' => $row['locationDynamicFields'],
                    'addressLatitude'      => $row['markerLatitude'],
                    'addressLongitude'     => $row['markerLongitude']
                ]);

                $legacyAddress->index($Template);

                continue;
            }

            // Ok, default action is to just save the row and queue it for later.
            $legacyAddress = $Addresses->create([
                'addressTitle'         => $row['locationTitle'],
                'addressBuilding'      => $row['locationBuilding'],
                'addressStreet'        => $row['locationStreet'],
                'addressTown'          => $row['locationTown'],
                'addressRegion'        => $row['locationRegion'],
                'addressPostcode'      => $row['locationPostcode'],
                'addressCountry'       => $row['locationPostcode'],
                'addressDynamicFields' => $row['locationDynamicFields']
            ]);

            $legacyAddress->index($Template);

            $Tasks->add('address.geocode', $legacyAddress->id());
        }

    } else {
        $Settings->set('root_locator_update', '2.0.0');
        PerchUtil::redirect($API->app_path());
    }
}

if (version_compare($Version, '3.0.0', '<')) {
    $sql = file_get_contents(__DIR__ . '/../sql/3.0.0.sql');
    $sql = str_replace('__PREFIX__', PERCH_DB_PREFIX, $sql);

    $statements = explode(';', $sql);
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if ($statement != '') {
            $db->execute($statement);
        }
    }

    $Paging = $API->get('Paging');
    $Paging->set_per_page(10);

    $updatable = $Addresses->all($Paging);

    if(PerchUtil::count($updatable)) {
        foreach($updatable as $update) {
            $update->update([
                'addressTitle' => $update->addressTitle()
            ]);

            $update->index($Template);
        }
    }

    if ($Paging->is_last_page()) {
        $Settings->set('root_locator_update', '3.0.0');
    }
}

if ($Paging && $Paging->is_last_page()) {
    $Settings->set('root_locator_update', '3.0.0');
}
