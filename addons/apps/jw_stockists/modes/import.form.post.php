<?php

// Side Panel UI
echo $HTML->side_panel_start();
echo $HTML->para('To import CSV data, upload a new file and then select it from the menu. Imported data will be queued for geocoding.');
echo $HTML->side_panel_end();

// Main Panel UI
echo $HTML->main_panel_start();
include('_subnav.php');

echo $HTML->heading1('Import Locations');

if ($message) {
    echo $message;
}

echo $HTML->heading2('Import Data');

echo $Form->form_start();

    if(PerchUtil::count($files)) {
        echo $Form->select_field('csv_path', 'Select CSV File', $files);
        echo $HTML->heading2('Upload New');
    }

    echo $Form->file_field('csv_file_upload', 'CSV Data', (isset($details['csv_file']) ? $details['csv_file'] : ''), PERCH_RESFILEPATH);
    echo $Form->submit_field('btnSubmit', 'Save', $API->app_path() . '/import');

echo $Form->form_end();
echo $HTML->main_panel_end();