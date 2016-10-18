<?php

PerchScheduledTasks::register_task('root_locator', 'geocode_location_batch', 10, 'root_locator_geocode_queued');

function jw_locator_process_geocoding_queue($last_run_date)
{
    $API = new PerchAPI(1.0, 'root_locator');
    $Tasks = new RootLocator_Tasks($API);

    $result = $Tasks->processQueue();

    if ($result === false) {
        return [
            'status'  => 'ERROR',
            'message' => 'There was an error processing the task queue'
        ];
    }

    return [
        'status'  => 'OK',
        'message' => sprintf('%s tasks processed', $result)
    ];
}