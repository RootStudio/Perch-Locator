<?php

if (!defined('PERCH_DB_PREFIX')) {
    exit;
}

$sql = "
    CREATE TABLE `__PREFIX__root_locator_addresses` (
      `addressID` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `addressTitle` varchar(255) NOT NULL DEFAULT '',
      `addressBuilding` varchar(255) DEFAULT '',
      `addressStreet` varchar(255) DEFAULT '',
      `addressTown` varchar(255) DEFAULT '',
      `addressRegion` varchar(255) DEFAULT '',
      `addressPostcode` varchar(15) DEFAULT '',
      `addressCountry` varchar(3) DEFAULT '',
      `addressLatitude` decimal(9,6),
      `addressLongitude` decimal(9,6),
      `addressDynamicFields` text,
      `addressUpdated` datetime NOT NULL,
      PRIMARY KEY (`addressID`)
    ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
";

$sql = str_replace('__PREFIX__', PERCH_DB_PREFIX, $sql);

// Install
$statements = explode(';', $sql);
foreach ($statements as $statement) {
    $statement = trim($statement);
    if ($statement != '') {
        $this->db->execute($statement);
    }
}

// Permissions
$API = new PerchAPI(1.0, 'root_locator');
$UserPrivileges = $API->get('UserPrivileges');
$UserPrivileges->create_privilege('root_locator', 'Access the locator app');
$UserPrivileges->create_privilege('root_locator.import', 'Mass import location data');

// Categories
$Core_CategorySets = new PerchCategories_Sets();
$Core_Categories = new PerchCategories_Categories();
$Set = $Core_CategorySets->get_by('setSlug', 'locator');

if (!$Set) {
    $Set = $Core_CategorySets->create(array(
        'setTitle'         => PerchLang::get('Locator'),
        'setSlug'          => 'locator',
        'setTemplate'      => '~/root_locator/templates/locator/category_set.html',
        'setCatTemplate'   => '~/root_locator/templates/locator/category.html',
        'setDynamicFields' => '[]'
    ));
}

// Installation check
$sql = 'SHOW TABLES LIKE "' . $this->table . '"';
$result = $this->db->get_value($sql);

return $result;