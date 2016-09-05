<?php

$Settings = $API->get('Settings');
$Paging = $API->get('Paging');
$Paging->set_per_page(10);

if ($Paging->is_first_page()) {
    $db = $API->get('DB');

    // v1.1.2
    $sql = 'ALTER TABLE `'. PERCH_DB_PREFIX .'jw_locator_locations` ADD `locationProcessingAttempts` INT(10) UNSIGNED NOT NULL DEFAULT 0 AFTER `locationProcessingStatus`';
    $db->execute($sql);

    $Settings->set('jw_locator_update', '1.1.2');
}
