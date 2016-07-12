<?php

$groups = [
    'A-D' => 'abcd',
    'E-H' => 'efgh',
    'I-L' => 'ijkl',
    'M-Q' => 'mopq',
    'R-V' => 'rstv',
    'W-Z' => 'wxyz',
    '0-9' => '0-9'
];

$smartbar_tabs = [];
$smartbar_tabs[] = '<li><span class="set">Filter</span></li>';

$smartbar_tabs[] = $HTML->smartbar_link(!isset($_GET['chars']), [
    'label' => $Lang->get('All'),
    'link'  => $API->app_path()
]);

foreach($groups as $label => $value) {
    $smartbar_tabs[] = $HTML->smartbar_link(isset($_GET['chars']) && $_GET['chars'] == $value, [
        'label' => $Lang->get($label),
        'link'  => $API->app_path() . '?chars=' . $value
    ]);
}

echo call_user_func_array([$HTML, 'smartbar'], $smartbar_tabs);