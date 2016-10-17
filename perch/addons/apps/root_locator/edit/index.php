<?php

include(__DIR__ . '/../../../../core/inc/api.php');

// Perch API
$API = new PerchAPI(1.0, 'root_locator');

// APIs
$Lang = $API->get('Lang');
$HTML = $API->get('HTML');
$Form = $API->get('Form');
$Template = $API->get('Template');
$Settings = $API->get('Settings');

// Page settings
$Perch->page_title = $Lang->get('Set Address');
$Perch->add_css($API->app_path() . '/assets/css/locator.css');

// Page Initialising
include('../modes/addresses.edit.pre.php');

// Perch Frame
include(PERCH_CORE . '/inc/top.php');

// Page
include('../modes/addresses.edit.post.php');

// Perch Frame
include(PERCH_CORE . '/inc/btm.php');
