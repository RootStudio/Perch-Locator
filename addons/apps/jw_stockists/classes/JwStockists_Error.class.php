<?php

/**
 * Class JwStockists_Error
 *
 * @author James Wigger <james.s.wigger@gmail.com>
 */
class JwStockists_Error extends PerchAPI_Base
{
    /**
     * Failed jobs table
     *
     * @var string
     */
    protected $table = 'jw_stockists_failed_jobs';

    /**
     * Primary Key
     *
     * @var string
     */
    protected $pk = 'errorID';
}
