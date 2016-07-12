<?php

/**
 * Class JwLocator_Helpers
 *
 * HTML Helpers and utilities for the Locator app
 *
 * @author James Wigger <james@rootstudio.co.uk>
 */
class JwLocator_Helpers
{
    /**
     * Output status badges for status values
     *
     * @param int $status_code
     */
    public static function status_tag($status_code)
    {
        $API = new PerchAPI('jw_locator', 1.0);
        $Lang = $API->get('Lang');

        switch($status_code) {
            case JwLocator_Location::STATUS_QUEUED:
                echo '<span class="tag queued">'. $Lang->get('In Queue') . '</span>';
                break;
            case JwLocator_Location::STATUS_PROCESSING:
                echo '<span class="tag processing">'. $Lang->get('Processing') . '</span>';
                break;
            case JwLocator_Location::STATUS_SYNCED:
                echo '<span class="tag synced">'. $Lang->get('Synced') . '</span>';
                break;
            case JwLocator_Location::STATUS_FAILED:
                echo '<span class="tag error">'. $Lang->get('Failed') . '</span>';
                break;
            case JwLocator_Location::STATUS_ERROR:
            default:
                echo '<span class="tag error">'. $Lang->get('Error') . '</span>';
                break;
        }
    }
}
