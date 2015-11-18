<?php include('../../../../core/inc/api.php');

// Perch API
$API = new PerchAPI(1.0, 'jw_stockists');

// Language instance
$Lang = $API->get('Lang');

// Page Meta
$Perch->page_title = $Lang->get('Delete Stockist Locations');

// Page Initialising
include('../modes/locations.delete.pre.php');

// Perch Frame
include(PERCH_CORE . '/inc/top.php');

// Page
include('../modes/locations.delete.post.php');

// Perch Frame
include(PERCH_CORE . '/inc/btm.php');