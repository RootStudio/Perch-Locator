<?php

/**
 * Class RootLocator_Task
 *
 * @author James Wigger <james@rootstudio.co.uk>
 */
class RootLocator_Task extends PerchAPI_Base
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
}
