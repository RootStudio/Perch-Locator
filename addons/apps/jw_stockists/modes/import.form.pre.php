<?php

// Load APIs
$HTML = $API->get('HTML');
$Form = $API->get('Form');
$Text = $API->get('Text');
$Template = $API->get('Template');

// Importer
$Importer = new JwStockists_Import();

$files = array();
$files = $Importer->get_imported_csv_files();

// Defaults
$message = false;

// Process Form
if ($Form->submitted()) {
    $postvars = array('csv_path');
    $data = $Form->receive($postvars);

    if (isset($_FILES['csv_file_upload']) && $_FILES['csv_file_upload']['name'] != '') {
        $Importer->upload_csv_file($_FILES['csv_file_upload']);
    } elseif (isset($data['csv_path']) && $data['csv_path'] != "") {
        $result = $Importer->import_csv_from_path($data['csv_path']);

        if($result > 0) {
            $message = $HTML->success_message($result . ' locations have been imported');
        } else {
            $message = $HTML->failure_message('There was a problem importing from that CSV file');
        }

    } else {
        $message = $HTML->failure_message('No action was selected');
    }
}
