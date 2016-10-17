<?php

/**
 * Class RootLocator_Tasks
 *
 * @author James Wigger <james@rootstudio.co.uk>
 */
class RootLocator_Tasks extends PerchAPI_Factory
{
    /**
     * Task queue table
     *
     * @var string
     */
    protected $table = 'root_locator_tasks';

    /**
     * Primary Key
     *
     * @var string
     */
    protected $pk = 'taskID';

    /**
     * Sort column
     *
     * @var string
     */
    protected $default_sort_column = 'taskStart';

    /**
     * Sort direction
     *
     * @var string
     */
    protected $default_sort_direction = 'ASC';

    /**
     * Factory singular class
     *
     * @var string
     */
    protected $singular_classname = 'RootLocator_Task';
}
