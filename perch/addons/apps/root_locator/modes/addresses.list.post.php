<?php

// Side Panel UI
echo $HTML->side_panel_start();
echo $HTML->para('This page shows you the current locations stored in the app. To edit a location, click on its title.');
echo $HTML->side_panel_end();

// Main Panel UI
echo $HTML->main_panel_start();

include(__DIR__ . '/_subnav.php');

echo '<a class="add button" href="' . $HTML->encode($API->app_path() . '/edit/') . '">' . $Lang->get('Add Address') . '</a>';
echo $HTML->heading1('Listing Addresses');

// Output alerts
$Alert->output();

?>

<?php if (PerchUtil::count($addresses)): ?>
    <table class="d">
        <thead>
            <tr>
                <th>
                    <?php echo $Lang->get('Title'); ?>
                </th>
                <th>
                    <?php echo $Lang->get('Building'); ?>
                </th>
                <th>
                    <?php echo $Lang->get('Town / City'); ?>
                </th>
                <th>
                    <?php echo $Lang->get('Postcode'); ?>
                </th>
                <th class="status-column">
                    <?php echo $Lang->get('Status'); ?>
                </th>
                <th class="action last">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
    <?php foreach ($addresses as $Address): ?>
        <tr>
            <td class="primary">
                <a href="<?php echo $HTML->encode($API->app_path()); ?>/edit/?id=<?php echo $HTML->encode(urlencode($Address->id())); ?>">
                    <?php echo $HTML->encode($Address->addressTitle()); ?>
                </a>
            </td>
            <td>
                <?php echo $HTML->encode($Address->addressBuilding()); ?>
            </td>
            <td>
                <?php echo $HTML->encode($Address->addressTown()); ?>
            </td>
            <td>
                <?php echo $HTML->encode($Address->addressPostcode()); ?>
            </td>
            <td class="status-column">
                <?php
                    if($Address->isGeocoded()) {
                        echo sprintf('<img src="%s/assets/images/status-%s.svg" alt="%s" class="status-icon %s" />', $API->app_path(), 'success', 'Geocoded', null);
                    } elseif($Address->isErrored()) {
                        echo sprintf('<img src="%s/assets/images/status-%s.svg" alt="%s" class="status-icon %s" />', $API->app_path(), 'error', 'Failed to geocode', null);
                    } else {
                        echo sprintf('<img src="%s/assets/images/status-%s.svg" alt="%s" class="status-icon %s" />', $API->app_path(), 'processing', 'Item is in queue', 'status-icon--rotate');
                    }
                ?>
            </td>
            <td>
                <a href="<?php echo $HTML->encode($API->app_path()); ?>/delete/?id=<?php echo $HTML->encode(urlencode($Address->id())); ?>"
                   class="delete inline-delete">
                    <?php echo $Lang->get('Delete'); ?>
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php

if ($Paging->enabled()) echo $HTML->paging($Paging);

// Main Panel UI
echo $HTML->main_panel_end();
