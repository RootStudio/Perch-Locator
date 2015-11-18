<?php
include PERCH_PATH . '/addons/apps/jw_stockists/classes/JwStockists_Locations.class.php';
include PERCH_PATH . '/addons/apps/jw_stockists/classes/JwStockists_Location.class.php';
include PERCH_PATH . '/addons/apps/jw_stockists/classes/JwStockists_Markers.class.php';
include PERCH_PATH . '/addons/apps/jw_stockists/classes/JwStockists_Marker.class.php';
include PERCH_PATH . '/addons/apps/jw_stockists/classes/JwStockists_Errors.class.php';
include PERCH_PATH . '/addons/apps/jw_stockists/classes/JwStockists_Error.class.php';

PerchScheduledTasks::register_task('jw_stockists', 'geocode_location_batch', 10, 'jw_stockists_process_geocoding_queue');

function jw_stockists_process_geocoding_queue($last_run_date)
{
    $API = new PerchAPI(1.0, 'jw_stockists');
    $Locations = new JwStockists_Locations($API);
    $Settings = $API->get('Settings');

    $batch_size = $Settings->get('jw_stockists_batch_size')->val();
    $locations = $Locations->get_queued($batch_size);
    $counter = 0;

    if(PerchUtil::count($locations)) {
        foreach($locations as $Location) {
            $Location->geocode();
            $counter++;
        }

        return array(
            'result' => 'OK',
            'message' => $counter . ' locations have been geocoded'
        );
    }
    else {
        return array(
            'result' => 'WARNING',
            'message' => 'The queue is empty'
        );
    }
}
