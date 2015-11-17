<?php

// Side Panel UI
echo $HTML->side_panel_start();
echo $HTML->para('This page shows you the current locations stored in the app. To edit a location, click on its title');
echo $HTML->side_panel_end();

// Main Panel UI
echo $HTML->main_panel_start();
include('_subnav.php');

echo '<a class="add button" href="' . $HTML->encode($API->app_path() . '/edit/') . '">' . $Lang->get('Add Location') . '</a>';
echo $HTML->heading1('Listing Locations');

?>

    <ul class="smartbar">
        <li class="selected">
            <a href="<?php echo PerchUtil::html($API->app_path()); ?>">
                <?php echo $Lang->get('All'); ?>
            </a>
        </li>
    </ul>

<?php if (isset($message)) {
    echo $message;
} ?>
<?php if (PerchUtil::count($locations)): ?>
    <table class="d">
        <thead>
            <tr>
                <th>
                    <?php echo $Lang->get('Title'); ?>
                </th>
                <th>
                    <?php echo $Lang->get('Building'); ?>
                </th>
                <th class="score-title">
                    <?php echo $Lang->get('Town / City'); ?>
                </th>
                <th class="score-title">
                    <?php echo $Lang->get('Postcode'); ?>
                </th>
                <th class="score-title">
                    <?php echo $Lang->get('Status'); ?>
                </th>
                <th class="action last">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($locations as $Location): ?>
                <td class="primary">
                    <a href="<?php echo $HTML->encode($API->app_path()); ?>/edit/?id=<?php echo $HTML->encode(urlencode($Location->id())); ?>">
                        <?php echo $HTML->encode($Location->locationTitle()); ?>
                    </a>
                </td>
                <td>
                    <?php echo $HTML->encode($Location->locationBuilding()); ?>
                </td>
                <td>
                    <?php echo $HTML->encode($Location->locationTown()); ?>
                </td>
                <td>
                    <?php echo $HTML->encode($Location->locationPostcode()); ?>
                </td>
                <td>
                    <?php echo $Location->get_status(); ?>
                </td>
                <td>
                    <?php if ($CurrentUser->has_priv('jw_stockists.delete')): ?>
                        <a href="<?php echo $HTML->encode($API->app_path()); ?>/delete/?id=<?php echo $HTML->encode(urlencode($Location->id())); ?>"
                           class="delete inline-delete">
                            <?php echo $Lang->get('Delete'); ?>
                        </a>
                    <?php endif; ?>
                </td>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($Paging->enabled()) {
        echo $HTML->paging($Paging);
    } ?>
<?php endif; ?>