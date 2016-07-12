<?php

// Side Panel UI
echo $HTML->side_panel_start();
echo $HTML->para('To import CSV data, upload a new file and then select it from the menu.');
echo $HTML->para('CSV data must have the following headings: <pre>%s</pre>', implode(',<br />', $Importer->csv_columns()));
echo $HTML->para('Any additional fields will be added as a dynamic value for use in templates.');
echo $HTML->para('To add categories, an additional column of <strong>categories</strong> can be added with a comma seperated list of category IDs.');
echo $HTML->para('Imported locations will be added to the queue to be plotted onto the map.');
echo $HTML->side_panel_end();

// Main Panel UI
echo $HTML->main_panel_start();
include('_subnav.php');

echo $HTML->heading1('Import Locations');

$Alert->output();

if($Importer->get_failed_rows() > 0) {
    echo $HTML->failure_message('%s rows could not be imported due to missing column data', $Importer->get_failed_rows());
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