<?php

include(__DIR__ . '/../../../../core/inc/api.php');

// Perch API
$API = new PerchAPI(1.0, 'root_locator');

// APIs
$Lang = $API->get('Lang');
$HTML = $API->get('HTML');
$Form = $API->get('Form');

// Page settings
$Perch->page_title = $Lang->get('Delete Address');

// Page Initialising
include('../modes/_subnav.php');
include('../modes/addresses.delete.pre.php');

// Perch Frame
include(PERCH_CORE . '/inc/top.php');

// Page
include('../modes/addresses.delete.post.php');

// Perch Frame
include(PERCH_CORE . '/inc/btm.php');
