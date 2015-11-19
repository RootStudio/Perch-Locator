<?php

PerchScheduledTasks::register_task('jw_locator', 'geocode_location_batch', 10, 'jw_locator_process_geocoding_queue');

function jw_locator_process_geocoding_queue($last_run_date)
{
    $API = new PerchAPI(1.0, 'jw_locator');
    $Locations = new JwLocator_Locations($API);
    $Settings = $API->get('Settings');

    $batch_size = $Settings->get('jw_locator_batch_size')->val();
    $locations = $Locations->get_queued($batch_size);
    $counter = 0;

    if(PerchUtil::count($locations)) {
        foreach($locations as $Location) {
            sleep(1);

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
