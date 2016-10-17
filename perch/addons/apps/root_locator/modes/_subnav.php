<?php

$Addresses = new RootLocator_Addresses;
$errorCount = $Addresses->totalErrored();

echo $HTML->subnav($CurrentUser, [
    [
        'page'  => ['root_locator', 'root_locator/edit'],
        'label' => 'Addresses',
        'priv'  => 'root_locator',
        'badge' => $errorCount
    ]
]);
