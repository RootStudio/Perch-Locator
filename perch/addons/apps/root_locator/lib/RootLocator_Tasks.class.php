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

    /**
     * Add a new task with pre-set data
     *
     * @param string $key
     * @param int    $addressID
     *
     * @return bool
     */
    public function add($key, $addressID)
    {
        if ($this->inQueue($key, $addressID)) {
            return false;
        }

        return $this->create([
            'taskKey'   => $key,
            'addressID' => $addressID,
            'taskStart' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Return a segment of the queued tasks limited by batch size
     *
     * @param string $key
     * @param int    $batch
     *
     * @return array|bool|SplFixedArray
     */
    public function getBatch($key, $batch = 25)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE `taskKey` = ' . $this->db->pdb($key);

        if (isset($this->default_sort_column)) {
            $sql .= ' ORDER BY ' . $this->default_sort_column . ' ' . $this->default_sort_direction;
        }

        $sql .= ' LIMIT ' . (int) $batch;

        $rows = $this->db->get_rows($sql);

        return $this->return_instances($rows);
    }

    /**
     * Delay all queue items 24 hours
     *
     * @return mixed
     */
    public function delayQueue()
    {
        $now = new DateTime();
        $now->add(new DateInterval('P1D'));

        $sql = 'UPDATE ' . $this->table . 'SET `taskStart` = ' . $now->format('Y-m-d H:i:s');

        return $this->db->execute($sql);
    }

    /**
     * Remove all items from queue
     *
     * @return mixed
     */
    public function clearQueue()
    {
        $sql = 'TRUNCATE TABLE ' . $this->table;

        return $this->db->execute($sql);
    }

    /**
     * Run through task queue and mass-geocode
     *
     * @param bool $delay
     *
     * @return int
     */
    public function processQueue($delay = false)
    {
        if (!$this->api) {
            PerchUtil::debug('Locator: Perch API must be set on Tasks class to process queue', 'error');

            return false;
        }

        $Addresses = new RootLocator_Addresses($this->api);
        $Geocoder = RootLocator_GeocoderFactory::createGeocoder();

        $Settings = $this->api->get('Settings');
        $batch = $Settings->get('root_locator_batch_size')->val();

        $tasks = $this->getBatch('address.geocode', $batch);
        $count = 0;

        if (!$tasks) {
            return $count;
        }

        foreach ($tasks as $Task) {
            $Address = $Addresses->find($Task->addressID());
            $result = $Geocoder->geocode($Address->fullAddress());

            if (!$Address) {
                PerchUtil::debug(sprintf('Locator: unable to process address `%s` - no record found', $Task->addressID()), 'error');
                $Task->delete();

                continue;
            }

            // Success, update the address and remove the task
            if (!$result->hasError()) {
                PerchUtil::debug('Locator: Geocoding success - clearing task', 'success');

                $coordinates = $result->getFirstCoordinates();

                $Address->update([
                    'addressLatitude'  => $coordinates['latitude'],
                    'addressLongitude' => $coordinates['longitude'],
                    'addressError'     => null
                ]);

                $Task->delete();
            }

            // Firstly, if our API limit has been reached then we need to try again tomorrow
            if ($result->hasError() && $result->getErrorKey() === 'quota_exceeded') {
                PerchUtil::debug('Locator: API Quota has been exceeded. Delaying queue.', 'notice');
                $this->delayQueue();
                break;
            }

            // If the task has not failed multiple times we can give it the benefit
            // of the doubt and retry it.
            if ($result->hasError() && !$Task->isLastAttempt()) {
                PerchUtil::debug('Locator: Geocoding failed - task reset for a new attempt', 'notice');
                $Task->requeue();
                continue;
            }

            // Ok, we tried everything and now we really do need
            // to tell the user what's gone wrong.
            if ($result->hasError() && $Task->isLastAttempt()) {
                PerchUtil::debug('Locator: Geocoding failed after multiple attempts. Clearing task and logging error.', 'error');

                $Address->update([
                    'addressLatitude'  => null,
                    'addressLongitude' => null,
                    'addressError'     => $result->getErrorKey()
                ]);

                $Task->delete(); // Admit defeat...
            }

            $count++;

            if($delay) {
                sleep((int) $delay);
            }
        }

        return $count;
    }

    /**
     * Check whether item already exists in queue
     *
     * @param int    $key
     * @param string $addressID
     *
     * @return bool
     */
    public function inQueue($key, $addressID)
    {
        $result = $this->db->get_count('
            SELECT COUNT(*) 
            FROM ' . $this->table . ' 
            WHERE `taskKey` = ' . $this->db->pdb($key) . ' 
            AND `addressID` = ' . $this->db->pdb($addressID)
        );

        return ($result > 0);
    }

    /**
     * Restrict all results to only tasks that can execute now
     *
     * @return string
     */
    protected function standard_restrictions()
    {
        return ' WHERE `taskStart >= ' . date('Y-m-d H:i:s');
    }
}
