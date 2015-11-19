<?php

/**
 * Class JwLocator_Marker
 *
 * @author James Wigger <james.s.wigger@gmail.com>
 */
class JwLocator_Marker extends PerchAPI_Base
{
    /**
     * Markers table
     *
     * @var string
     */
    protected $table = 'jw_locator_markers';

    /**
     * Primary Key
     *
     * @var string
     */
    protected $pk = 'markerID';

    /**
     * JwLocator_Marker constructor.
     *
     * Fix for missing markerDistance property
     *
     * @param array $details
     */
    public function __construct($details)
    {
        if(!isset($details['markerDistance'])) {
            $details['markerDistance'] = false;
        }

        parent::__construct($details);
    }
}
