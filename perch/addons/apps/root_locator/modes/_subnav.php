<?php

echo $HTML->subnav($CurrentUser, [
    [
        'page'  => ['root_locator', 'root_locator/edit'],
        'label' => 'Addresses',
        'priv'  => 'root_locator'
    ]
]);
