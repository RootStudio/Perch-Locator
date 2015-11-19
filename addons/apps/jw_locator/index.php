<?php include('../../../core/inc/api.php');

// Perch API
$API = new PerchAPI(1.0, 'jw_locator');

// Language instance
$Lang = $API->get('Lang');

// Page Meta
$Perch->page_title = $Lang->get('Manage Locations - Perch');
$Perch->add_css($API->app_path() . '/assets/css/locator.css');

// Page Initialising
include('modes/locations.list.pre.php');

// Perch Frame
include(PERCH_CORE . '/inc/top.php');

// Page
include('modes/locations.list.post.php');

// Perch Frame
include(PERCH_CORE . '/inc/btm.php');