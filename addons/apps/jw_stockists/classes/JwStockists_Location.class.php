<?php

class JwStockists_Location extends PerchAPI_Base
{
    protected $table = 'jw_stockists_locations';

    protected $pk = 'locationID';

    public function update($data, $ignore_timestamp = false)
    {
        if(!$ignore_timestamp) {
            $data['locationUpdatedAt'] = date("Y-m-d H:i:s");
        }

        parent::update($data);
    }

    public function get_status()
    {
        switch((int) $this->locationProcessingStatus())
        {
            case 1:
                return '<span class="tag queued">In Queue</span>';
                break;
            case 2:
                return '<span class="tag processing">Processing</span>';
                break;
            case 3:
                return '<span class="tag synced">Synced</span>';
                break;
            case 4:
                return '<span class="tag error">Failed</span>';
                break;
            default:
                return '<span class="tag error">Error</span>';
                break;
        }
    }
}
