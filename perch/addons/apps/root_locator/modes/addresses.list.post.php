<?php

// Side Panel UI
echo $HTML->side_panel_start();
echo $HTML->para('This page shows you the current locations stored in the app. To edit a location, click on its title.');
echo $HTML->side_panel_end();

// Main Panel UI
echo $HTML->main_panel_start();

include(__DIR__ . '/_subnav.php');

echo '<a class="add button" href="' . $HTML->encode($API->app_path() . '/edit/') . '">' . $Lang->get('Add Address') . '</a>';
echo $HTML->heading1('Listing Addresses');

// Output alerts
$Alert->output();

?>

<?php

if ($Paging->enabled()) echo $HTML->paging($Paging);

// Main Panel UI
echo $HTML->main_panel_end();
