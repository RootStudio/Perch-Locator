<?php
    // Title
    echo $HTML->title_panel([
        'heading' => $Lang->get('Software Update')
    ], $CurrentUser);
?>

    <?php if (!$Paging->is_last_page()): ?>
        <div class="inner">
            <ul class="progress-list">
                <li class="progress-item progress-success">
                    <?php echo PerchUI::icon('core/circle-check', '16', 'Success'); ?>
                    <?php echo $Lang->get('Importing legacy locations %s to %s of %s', $Paging->lower_bound(), $Paging->upper_bound(), $Paging->total()); ?>
                </li>
            </ul>
        </div>
    <?php else: ?>
        <?php echo $HTML->success_block('Update Complete', 'You should now manually remove the previous database tables: "perch2_jw_locator_locations", "perch2_jw_locator_markers", "perch2_jw_locator_failed_jobs".'); ?>
        <div class="inner">
            <a href="<?php echo $API->app_path(); ?>" class="button button-simple">Continue</a>
        </div>
    <?php endif; ?>

<?php
if (!$Paging->is_last_page()) {
    $paging = $Paging->to_array();
    echo "
    <script>
        window.setTimeout(function(){
            window.location='" . PerchUtil::html($paging['next_url'], true) . "';
        }, 0);
    </script>";
}
?>