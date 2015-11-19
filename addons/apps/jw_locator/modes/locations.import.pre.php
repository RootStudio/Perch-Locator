<?php

if (!$CurrentUser->has_priv('jw_locator.import')) {
    PerchUtil::redirect($API->app_path());
}

// Load APIs
$HTML = $API->get('HTML');
$Form = $API->get('Form');
$Text = $API->get('Text');
$Template = $API->get('Template');

// Importer
$Importer = new JwLocator_Import();

$files = array();
$files = $Importer->get_imported_csv_files();

// Defaults
$message = false;

if (!$Importer->import_dir_writable()) {
    $message = $HTML->warning_message('Your resources folder is not writable. Make this folder writable if you want to upload files.');
}

// Process Form
if ($Form->submitted()) {
    $postvars = array('csv_path');
    $data = $Form->receive($postvars);

    if (isset($_FILES['csv_file_upload']) && $_FILES['csv_file_upload']['name'] != '') {
        $result = $Importer->upload_csv_file($_FILES['csv_file_upload']);

        if($result) {
            PerchUtil::redirect($API->app_path() . '/import/?created=1');
        }

    } elseif (isset($data['csv_path']) && $data['csv_path'] != "") {
        $result = $Importer->import_csv_from_path($data['csv_path']);

        if ($result > 0) {
            $message = $HTML->success_message($result . ' locations have been imported');
        } else {
            $message = $HTML->failure_message('There was a problem importing from that CSV file.');
        }

    } else {
        $message = $HTML->failure_message('No action has been selected - have you selected a file to import / upload?');
    }
}

// Success
if (isset($_GET['created']) && !$message) {
    $message = $HTML->success_message('The CSV file uploaded successfully. You can now import data.');
}
