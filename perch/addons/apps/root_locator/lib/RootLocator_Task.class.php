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

    /**
     * Send task to back of the queue
     *
     * @return mixed
     */
    public function requeue()
    {
        return $this->update([
            'taskAttempt' => $this->taskAttempt() + 1,
            'taskStart' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Return whether the task has failed the maximum times
     *
     * @return bool
     */
    public function isLastAttempt()
    {
        return $this->taskAttempt() === 3;
    }
}
