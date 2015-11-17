<?php

if (!defined('PERCH_DB_PREFIX')) {
    exit;
}

// Create Tables
$sql = "
CREATE TABLE `__PREFIX__jw_stockists_locations` (
  `locationID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `locationTitle` varchar(255) NOT NULL DEFAULT '',
  `locationBuilding` varchar(255) NOT NULL DEFAULT '',
  `locationStreet` varchar(255) NOT NULL DEFAULT '',
  `locationTown` varchar(255) NOT NULL DEFAULT '',
  `locationRegion` varchar(255) DEFAULT '',
  `locationCountry` varchar(3) NOT NULL DEFAULT '',
  `locationPostcode` varchar(15) NOT NULL DEFAULT '',
  `locationDynamicFields` text,
  `locationUpdatedAt` datetime NOT NULL,
  `locationProcessedAt` datetime NOT NULL,
  `locationProcessingStatus` tinyint(1) NOT NULL DEFAULT '1',
  `markerID` int(11) unsigned DEFAULT '0',
  PRIMARY KEY (`locationID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

CREATE TABLE `perch2_jw_stockists_markers` (
  `markerID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `markerLatitude` decimal(9,6) NOT NULL DEFAULT '0.000000',
  `markerLongitude` decimal(9,6) NOT NULL DEFAULT '0.000000',
  PRIMARY KEY (`markerID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

INSERT INTO `__PREFIX__settings` (`settingID`, `userID`, `settingValue`)
VALUES ('jw_stockists_batch_size', 0, 25);";

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
$API = new PerchAPI(1.0, 'jw_stockists');
$UserPrivileges = $API->get('UserPrivileges');
$UserPrivileges->create_privilege('jw_stockists', 'Access the locator app');
$UserPrivileges->create_privilege('jw_stockists.delete', 'Delete locations');
$UserPrivileges->create_privilege('jw_stockists.import', 'Mass import location data');

// Categories
$Core_CategorySets = new PerchCategories_Sets();
$Core_Categories = new PerchCategories_Categories();
$Set = $Core_CategorySets->get_by('setSlug', 'locator');

if (!$Set) {
    $Set = $Core_CategorySets->create(array(
        'setTitle'         => PerchLang::get('Locator'),
        'setSlug'          => 'locator',
        'setTemplate'      => '~/jw_stockists/templates/locator/category_set.html',
        'setCatTemplate'   => '~/jw_stockists/templates/locator/category.html',
        'setDynamicFields' => '[]'
    ));
}

// Installation check
$sql = 'SHOW TABLES LIKE "' . $this->table . '"';
$result = $this->db->get_value($sql);

return $result;