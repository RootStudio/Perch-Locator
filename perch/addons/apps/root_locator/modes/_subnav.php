<?php

$Addresses = new RootLocator_Addresses;
$errorCount = $Addresses->totalWithErrors();

PerchUI::set_subnav([
    [
        'page'  => ['root_locator', 'root_locator/edit', 'root_locator/delete'],
        'label' => 'Add/Edit',
        'priv'  => 'root_locator',
        'badge' => $errorCount
    ],
    [
        'page'  => ['root_locator/import'],
        'label' => 'Import',
        'priv'  => 'root_locator.import'
    ]
]);
