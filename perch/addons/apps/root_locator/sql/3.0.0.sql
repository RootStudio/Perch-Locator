ALTER TABLE `__PREFIX__root_locator_addresses` ADD `addressSlug` varchar(255) NOT NULL DEFAULT '' AFTER `addressID`;

INSERT IGNORE INTO `__PREFIX__settings` (`settingID`, `userID`, `settingValue`)
VALUES ('root_locator_address_url', 0, '/locations/view.php?s={addressSlug}');

INSERT IGNORE INTO `__PREFIX__settings` (`settingID`, `userID`, `settingValue`)
VALUES ('root_locator_address_slug', 0, '{addressTitle}-{addressPostcode}');
