<?php

include(__DIR__ . '/../../../../core/inc/api.php');

if (!$CurrentUser->has_priv('root_locator.import')) {
    PerchUtil::redirect($API->app_path());
}

// Perch API
$API = new PerchAPI(1.0, 'root_locator');

// APIs
$Lang = $API->get('Lang');
$HTML = $API->get('HTML');
$Form = $API->get('Form');

// Page settings
$Perch->page_title = $Lang->get('Import Addresses');
$Perch->add_css($API->app_path() . '/assets/css/locator.css');

// Page Initialising
include('../modes/_subnav.php');
include('../modes/import.pre.php');

// Perch Frame
include(PERCH_CORE . '/inc/top.php');

// Page
include('../modes/import.post.php');

// Perch Frame
include(PERCH_CORE . '/inc/btm.php');
