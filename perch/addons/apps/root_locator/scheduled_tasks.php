<?php

PerchScheduledTasks::register_task('root_locator', 'geocode_location_batch', 10, 'root_locator_process_task_queue');

function root_locator_process_task_queue($last_run_date)
{
    include(__DIR__ . '/lib/vendor/autoload.php');

    spl_autoload_register(function ($class_name) {
        if (strpos($class_name, 'RootLocator') === 0) {
            include(PERCH_PATH . '/addons/apps/root_locator/lib/' . $class_name . '.class.php');

            return true;
        }

        return false;
    });

    $API = new PerchAPI(1.0, 'root_locator');
    $Tasks = new RootLocator_Tasks($API);

    $result = $Tasks->processQueue(1);

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