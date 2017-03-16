<?php

// Side Panel UI
echo $HTML->side_panel_start();
echo $HTML->para('CSV Data must include the following columns:');
echo $HTML->para('<pre>addressTitle (required)<br/>addressBuilding (required)<br/>addressStreet (recommended)<br/>addressTown<br/>addressRegion<br/>addressPostcode (required)<br/>addressCountry</pre>');
echo $HTML->para('Rows that are missing any of the required fields will not be imported. Those missing recommended fields will be imported but may fail in the geocoding queue.');
echo $HTML->side_panel_end();

// Main Panel UI
echo $HTML->main_panel_start();

include(__DIR__ . '/_subnav.php');

echo $HTML->heading1('Upload CSV');

$Alert->output();

?>

<?php if(PerchUtil::count($results)): ?>
    <ul class="importables">
        <?php foreach($results as $result): ?>
        <li class="icon <?php echo $result['status']; ?>">
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
}

// Main Panel UI
echo $HTML->main_panel_end();
