<?php

if (!$CurrentUser->has_priv('jw_locator.import')) {
    PerchUtil::redirect($API->app_path());
}

// Load APIs
$HTML = $API->get('HTML');
$Form = $API->get('Form');
$Text = $API->get('Text');
$Settings = $API->get('Settings');
$Template = $API->get('Template');

// Importer
$Importer = new JwLocator_Import();

$files = array();
$files = $Importer->get_imported_csv_files();

// Defaults
$message = false;

if (!$Importer->import_dir_writable()) {
    $Alert->set('notice', $Lang->get('Your resources folder is not writable. Make this folder writable if you want to upload files.'));
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
            $Alert->set('success', $Lang->get('%s locations have been imported', $result));
        } else {
            $Alert->set('error', $Lang->get('There was a problem importing from that CSV file.'));
        }

    } else {
        $Alert->set('error', $Lang->get('No action has been completed - have you selected a file to import / upload?'));
    }
}

// Scoop up failed rows
if($Importer->get_failed_rows() > 0) {
   $Alert->set('error', $Lang->get('%s rows could not be imported due to missing column data', $Importer->get_failed_rows()));
}

// Success
if (isset($_GET['created']) && !$message) {
    $message = $HTML->success_message('The CSV file uploaded successfully. You can now import data.');
}

if(!$Settings->get('jw_locator_google_api_key')->val()) {
    return $Alert->set('notice', $Lang->get('There is no Google API key set. Please add your key using the Perch settings area.'));
}
