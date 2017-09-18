<?php

if (!defined('PERCH_DB_PREFIX')) {
    exit;
}

$sql = file_get_contents(__DIR__ . '/sql/schema.sql');
$sql = file_get_contents(__DIR__ . '/sql/3.0.0.sql');
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