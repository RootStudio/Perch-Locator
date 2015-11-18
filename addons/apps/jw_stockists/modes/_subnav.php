<?php

$subnav_pages = array();
$Errors = new JwStockists_Errors();

$subnav_pages[] = array(
    'page' => array('jw_stockists', 'jw_stockists/edit'),
    'label' => $Lang->get('Locations'),
    'priv' => 'jw_stockists'
);

$subnav_pages[] = array(
    'page' => array('jw_stockists/import'),
    'label' => $Lang->get('Import Data'),
    'priv' => 'jw_stockists.import'
);

$subnav_pages[] = array(
    'page' => array('jw_stockists/errors'),
    'label' => $Lang->get('Failed Jobs'),
    'priv' => 'jw_stockists.import',
    'badge' => $Errors->total()
);

echo $HTML->subnav($CurrentUser, $subnav_pages);
unset($subnav_pages);
