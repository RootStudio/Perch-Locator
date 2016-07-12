<?php

$subnav_pages = array();
$Errors = new JwLocator_Errors();

$subnav_pages[] = array(
    'page' => array('jw_locator', 'jw_locator/edit'),
    'label' => $Lang->get('Locations'),
    'priv' => 'jw_locator'
);

$subnav_pages[] = array(
    'page' => array('jw_locator/import'),
    'label' => $Lang->get('Import Data'),
    'priv' => 'jw_locator.import'
);

$subnav_pages[] = array(
    'page' => array('jw_locator/errors'),
    'label' => $Lang->get('Failed Jobs'),
    'priv' => 'jw_locator.errors',
    'badge' => $Errors->total()
);

echo $HTML->subnav($CurrentUser, $subnav_pages);

unset($Errors);
unset($subnav_pages);
