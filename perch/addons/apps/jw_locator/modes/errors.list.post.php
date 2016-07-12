<?php

// Side Panel UI
echo $HTML->side_panel_start();
echo $HTML->para('This page lists all locations that could not be plotted onto the map.');
echo $HTML->para('Common reasons for failure are incorrect addresses or reaching the API limit.');
echo $HTML->para('If the API limit has been reached, contact your developer to request a new key');
echo $HTML->side_panel_end();

// Main Panel UI
echo $HTML->main_panel_start();
include('_subnav.php');

echo $HTML->heading1('Listing Locations');

$Alert->output();
?>

<ul class="smartbar">
    <li class="selected">
        <a href="<?php echo PerchUtil::html($API->app_path()); ?>">
            <?php echo $Lang->get('All'); ?>
        </a>
    </li>
</ul>

<?php if (PerchUtil::count($locations)): ?>
    <table class="d error-list">
        <thead>
            <tr>
                <th>
                    <?php echo $Lang->get('Title'); ?>
                </th>
                <th class="score-title">
                    <?php echo $Lang->get('Error'); ?>
                </th>
                <th class="score-title">
                    <?php echo $Lang->get('Date'); ?>
                </th>
                <th class="action last">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($locations as $Location): ?>
            <tr>
                <td class="primary">
                    <?php echo $HTML->encode($Location->locationTitle()); ?>
                </td>
                <td>
                    <?php
                        $Error = $Location->get_error();
                        echo $HTML->encode($Error ? $Error->errorMessage() : null);
                    ?>
                </td>
                <td>
                    <?php
                        $Error = $Location->get_error();
                        echo $HTML->encode($Error ? $Error->errorDateTime() : null);
                    ?>
                </td>
                <td>
                    <?php if ($CurrentUser->has_priv('jw_locator.delete')): ?>
                        <a href="<?php echo $HTML->encode($API->app_path()); ?>/edit/?id=<?php echo $HTML->encode(urlencode($Location->id())); ?>"
                           class="delete">
                            <?php echo $Lang->get('Edit'); ?>
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
