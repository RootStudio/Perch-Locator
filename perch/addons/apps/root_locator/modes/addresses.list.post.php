<?php

// Title
echo $HTML->title_panel([
    'heading' => $Lang->get('Listing Addresses'),
    'button'  => [
        'text' => $Lang->get('Add Address'),
        'link' => $API->app_nav().'/edit/',
        'icon' => 'core/plus'
    ],
], $CurrentUser);

// Smartbar
$Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

$Smartbar->add_item([
    'active' => (!isset($_GET['chars']) && !isset($_GET['filter']) && !isset($_GET['show-filter'])),
    'title' => 'All',
    'link'  => $API->app_nav(),
    'icon' => 'core/menu'
]);

$Smartbar->add_item([
    'active' => (isset($_GET['filter']) && $_GET['filter'] == 'complete'),
    'title' => 'Complete',
    'link'  => $API->app_nav() . '?filter=complete',
    'icon' => 'core/circle-check'
]);

$Smartbar->add_item([
    'active' => (isset($_GET['filter']) && $_GET['filter'] == 'failed'),
    'title' => 'Failed',
    'link'  => $API->app_nav() . '?filter=failed',
    'icon' => 'core/alert'
]);

$Smartbar->add_item([
    'id' => 'chars',
    'title' => 'By Title',
    'icon' => 'core/o-typewriter',
    'active' => PerchRequest::get('chars'),
    'type'   => 'filter',
    'arg' => 'chars',
    'options' => [
        ['value' => 'abcd', 'title' => 'A-D'],
        ['value' => 'efgh', 'title' => 'E-H'],
        ['value' => 'ijkl', 'title' => 'I-L'],
        ['value' => 'mopq', 'title' => 'M-Q'],
        ['value' => 'rstv', 'title' => 'R-V'],
        ['value' => 'wxyz', 'title' => 'W-Z'],
        ['value' => '0-9', 'title' => '0-9']
    ],
    'actions' => []
]);

echo $Smartbar->render();

// Alerts
$Alert->output();

// Listing
$Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);

$Listing->add_col([
    'title'     => 'Title',
    'value'     => 'addressTitle',
    'sort'      => 'addressTitle',
    'edit_link' => 'edit'
]);

$Listing->add_col([
    'title' => 'Building',
    'value' => 'addressBuilding'
]);

$Listing->add_col([
    'title' => 'Town / City',
    'value' => 'addressTown'
]);

$Listing->add_col([
    'title' => 'Postcode',
    'value' => 'addressPostcode'
]);

$Listing->add_col([
    'title' => 'Status',
    'value' => function($Address) use($API) {
        if($Address->hasCoordinates()) {
            return sprintf('<img src="%s/assets/images/status-%s.svg" alt="%s" class="status-icon %s" />', $API->app_path(), 'success', 'Geocoded', null);
        } elseif($Address->hasError()) {
            return sprintf('<img src="%s/assets/images/status-%s.svg" alt="%s" class="status-icon %s" />', $API->app_path(), 'error', 'Failed to geocode', null);
        } else {
            return sprintf('<img src="%s/assets/images/status-%s.svg" alt="%s" class="status-icon %s" />', $API->app_path(), 'processing', 'Item is in queue', 'status-icon--rotate');
        }
    },
    'class' => 'status-cell'
]);

$Listing->add_delete_action([
    'inline' => true,
    'path'   => 'delete',
]);

echo $Listing->render($addresses);
