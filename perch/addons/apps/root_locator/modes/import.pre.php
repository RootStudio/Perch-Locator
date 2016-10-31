<?php

$server_supported = true;
$results = false;


if (!extension_loaded('mbstring')) {
    $Alert->set('error', $Lang->get('The mbstring extension must be installed on the server. Imports are disabled.'));
    $server_supported = false;
}

if($Form->submitted()) {
    $file = $_FILES['csv_import'];

    if($file['error'] === UPLOAD_ERR_OK && is_uploaded_file($file['tmp_name'])) {
        $file = new SplFileObject($file['tmp_name']);
        $required = ['addressTitle', 'addressBuilding', 'addressPostcode'];
        $recommended = ['addressStreet'];

        $Importer = RootLocator_ImporterFactory::createImporter($file, $required, $recommended);
        $Importer->import();

        $results = $Importer->getResults();

        if($Importer->getErrorTotal() > 0) {
            $Alert->set('error', $Lang->get('%s rows could not be imported due to missing required columns', $Importer->getErrorTotal()));
        }

        if($Importer->getWarningTotal() > 0) {
            $Alert->set('notice', $Lang->get('%s rows have warnings due to lack of data', $Importer->getWarningTotal()));
        }

        $Alert->set('success', $Lang->get('%s rows have been imported successfully', $Importer->getSuccessTotal()));

    } else {
        $Alert->set('error', $Lang->get('You must upload a CSV file'));
    }

    PerchUtil::debug($file);
}
