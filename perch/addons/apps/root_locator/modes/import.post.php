<?php

// Title
echo $HTML->title_panel([
    'heading' => $Lang->get('Upload CSV')
], $CurrentUser);

$Alert->output();

?>

<?php if(PerchUtil::count($results)): ?>
    <ul class="progress-list">
        <?php foreach($results as $result): ?>
        <li class="progress-item progress-<?php echo $result['status']; ?>">
            <?php
                switch($result['status']) {
                    case 'success':
                        echo PerchUI::icon('core/circle-check');
                        break;
                    case 'warning':
                        echo PerchUI::icon('core/alert');
                        break;
                    case 'failure':
                        echo PerchUI::icon('core/face-pain');
                        break;
                }
            ?>
            <?php echo $result['row']; ?>
            <?php if($result['message']): ?>
                <span class="status-message">
                    <?php echo $result['message']; ?>
                </span>
            <?php endif; ?>
        </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php

if($server_supported) {
    echo $Form->form_start();
    echo $Form->file_field('csv_import', 'CSV data file', false, PERCH_RESFILEPATH);
    echo $Form->submit_field('btnSubmit', 'Import', $API->app_path());
    echo $Form->form_end();

    echo $HTML->open('div.inner');
    echo $HTML->para('CSV Data must include the following columns:');
    echo $HTML->para('<pre>addressTitle (required)<br/>addressBuilding (required)<br/>addressStreet (recommended)<br/>addressTown<br/>addressRegion<br/>addressPostcode (required)<br/>addressCountry</pre>');
    echo $HTML->para('Rows that are missing any of the required fields will not be imported. Those missing recommended fields will be imported but may fail in the geocoding queue.');
    echo $HTML->close('div');
}
