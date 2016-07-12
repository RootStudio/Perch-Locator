<?php

/**
 * Class JwLocator_Error
 *
 * @author James Wigger <james.s.wigger@gmail.com>
 */
class JwLocator_Error extends PerchAPI_Base
{
    /**
     * Failed jobs table
     *
     * @var string
     */
    protected $table = 'jw_locator_failed_jobs';

    /**
     * Primary Key
     *
     * @var string
     */
    protected $pk = 'errorID';
}
