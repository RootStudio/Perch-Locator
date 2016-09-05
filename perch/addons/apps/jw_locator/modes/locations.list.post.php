<?php

// Side Panel UI
echo $HTML->side_panel_start();
echo $HTML->para('This page shows you the current locations stored in the app. To edit a location, click on its title.');
echo $HTML->para('Locations are queued to be plotted on the map, you can see the status with the "status" tag.');
echo $HTML->para('There are 4 possible statuses: In Queue, Processing, Synced and Failed');
echo $HTML->para('Due to a change in Google\'s API policy, an key is now required. Please create one using the <a href="https://console.developers.google.com" target="_blank">Google API Console</a> and ensure that the Maps API is enabled.');
echo $HTML->side_panel_end();

// Main Panel UI
echo $HTML->main_panel_start();
include('_subnav.php');

echo '<a class="add button" href="' . $HTML->encode($API->app_path() . '/edit/') . '">' . $Lang->get('Add Location') . '</a>';
echo $HTML->heading1('Listing Locations');

// Output alerts
$Alert->output();

// Include filter bar partial
include 'partials/char-filter.php';

// List items
if (PerchUtil::count($locations)): ?>
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
            <tr>
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
                    <?php if($Location->is_failed()) {
                        echo '<a href="'. $HTML->encode($API->app_path()) .'/errors/?view='. $HTML->encode(urlencode($Location->id())) .'">';
                    } ?>

                    <?php JwLocator_Helpers::status_tag($Location->locationProcessingStatus()); ?>

                    <?php if($Location->is_failed()) {
                        echo '</a>';
                    } ?>
                </td>
                <td>
                    <?php if ($CurrentUser->has_priv('jw_locator.delete')): ?>
                        <a href="<?php echo $HTML->encode($API->app_path()); ?>/delete/?id=<?php echo $HTML->encode(urlencode($Location->id())); ?>"
                           class="delete inline-delete">
                            <?php echo $Lang->get('Delete'); ?>
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($Paging->enabled()) {
        echo $HTML->paging($Paging);
    } ?>
<?php endif; ?>