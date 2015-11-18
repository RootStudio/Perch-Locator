<?php include('../../../../core/inc/api.php');

// Perch API
$API = new PerchAPI(1.0, 'jw_stockists');

// Language instance
$Lang = $API->get('Lang');

// Page Meta
$Perch->page_title = $Lang->get('Import Stockist Locations');
$Perch->add_css($API->app_path() . '/assets/css/locator.css');

// Page Initialising
include('../modes/import.form.pre.php');

// Perch Frame
include(PERCH_CORE . '/inc/top.php');

// Page
include('../modes/import.form.post.php');

// Perch Frame
include(PERCH_CORE . '/inc/btm.php');