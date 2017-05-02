<?php

// Side Panel UI
echo $HTML->side_panel_start();
echo $HTML->para('This page shows you the current locations stored in the app. To edit a location, click on its title.');
echo $HTML->side_panel_end();

// Main Panel UI
echo $HTML->main_panel_start();

echo $HTML->heading1('Delete Address');

echo $Form->form_start();
    echo $HTML->warning_message('Are you sure you wish to delete the address ‘%s’', $details['addressTitle']);
    echo $Form->hidden('addressID', $details['addressID']);
    echo $Form->submit_field('btnSubmit', 'Delete', $API->app_path());
echo $Form->form_end();

// Main Panel UI
echo $HTML->main_panel_end();
