<?php include('../../../../core/inc/api.php');

// Perch API
$API = new PerchAPI(1.0, 'jw_stockists');

// Language instance
$Lang = $API->get('Lang');

// Page Meta
$Perch->page_title = $Lang->get('Failed Stockist Locations');
$Perch->add_css($API->app_path() . '/assets/css/locator.css');

// Page Initialising
include('../modes/errors.list.pre.php');

// Perch Frame
include(PERCH_CORE . '/inc/top.php');

// Page
include('../modes/errors.list.post.php');

// Perch Frame
include(PERCH_CORE . '/inc/btm.php');