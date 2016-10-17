<?php

include(__DIR__ . '/../../../core/inc/api.php');

// Perch API
$API = new PerchAPI(1.0, 'root_locator');

// APIs
$Lang = $API->get('Lang');
$HTML = $API->get('HTML');
$Settings = $API->get('Settings');

// Page settings
$Perch->page_title = $Lang->get('Locator');
$Perch->add_css($API->app_path() . '/assets/css/locator.css');

// Page Initialising
include('modes/addresses.list.pre.php');

// Perch Frame
include(PERCH_CORE . '/inc/top.php');

// Page
include('modes/addresses.list.post.php');

// Perch Frame
include(PERCH_CORE . '/inc/btm.php');
