<?php

if (!defined('PERCH_DB_PREFIX')) {
    exit;
}

// Create Tables
$sql = "
CREATE TABLE `__PREFIX__jw_locator_locations` (
  `locationID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `locationTitle` varchar(255) NOT NULL DEFAULT '',
  `locationBuilding` varchar(255) DEFAULT '',
  `locationStreet` varchar(255) DEFAULT '',
  `locationTown` varchar(255) DEFAULT '',
  `locationRegion` varchar(255) DEFAULT '',
  `locationCountry` varchar(3) DEFAULT '',
  `locationPostcode` varchar(15) DEFAULT '',
  `locationDynamicFields` text,
  `locationUpdatedAt` datetime NOT NULL,
  `locationProcessedAt` datetime NOT NULL,
  `locationProcessingStatus` tinyint(1) NOT NULL DEFAULT '1',
  `markerID` int(11) unsigned DEFAULT '0',
  PRIMARY KEY (`locationID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

CREATE TABLE `__PREFIX__jw_locator_markers` (
  `markerID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `markerLatitude` decimal(9,6) NOT NULL DEFAULT '0.000000',
  `markerLongitude` decimal(9,6) NOT NULL DEFAULT '0.000000',
  PRIMARY KEY (`markerID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

CREATE TABLE `__PREFIX__jw_locator_failed_jobs` (
  `errorID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `errorMessage` text NOT NULL,
  `errorDateTime` datetime NOT NULL,
  `locationID` int(11) unsigned NOT NULL,
  PRIMARY KEY (`errorID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

INSERT INTO `__PREFIX__settings` (`settingID`, `userID`, `settingValue`)
VALUES ('jw_locator_batch_size', 0, 25);";

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
$API = new PerchAPI(1.0, 'jw_locator');
$UserPrivileges = $API->get('UserPrivileges');
$UserPrivileges->create_privilege('jw_locator', 'Access the locator app');
$UserPrivileges->create_privilege('jw_locator.delete', 'Delete locations');
$UserPrivileges->create_privilege('jw_locator.import', 'Mass import location data');
$UserPrivileges->create_privilege('jw_locator.errors', 'View error log');

// Categories
$Core_CategorySets = new PerchCategories_Sets();
$Core_Categories = new PerchCategories_Categories();
$Set = $Core_CategorySets->get_by('setSlug', 'locator');

if (!$Set) {
    $Set = $Core_CategorySets->create(array(
        'setTitle'         => PerchLang::get('Locator'),
        'setSlug'          => 'locator',
        'setTemplate'      => '~/jw_locator/templates/locator/category_set.html',
        'setCatTemplate'   => '~/jw_locator/templates/locator/category.html',
        'setDynamicFields' => '[]'
    ));
}

// Installation check
$sql = 'SHOW TABLES LIKE "' . $this->table . '"';
$result = $this->db->get_value($sql);

return $result;