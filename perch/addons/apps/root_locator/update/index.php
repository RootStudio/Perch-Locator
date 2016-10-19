<?php

include(__DIR__ . '/../../../../core/inc/api.php');

// Perch API
$API = new PerchAPI(1.0, 'root_locator');

// APIs
$Lang = $API->get('Lang');
$Paging = $API->get('Paging');
$db = $API->get('DB');

// Page settings
$Perch->page_title = $Lang->get('Update Locator');

// Page Initialising
include('../modes/update.pre.php');

// Perch Frame
include(PERCH_CORE . '/inc/top.php');

// Page
include('../modes/update.post.php');

// Perch Frame
include(PERCH_CORE . '/inc/btm.php');
