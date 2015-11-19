<?php

/**
 * Class JwLocator_QueryCache
 *
 * @author James Wigger <james.s.wigger@gmail.com>
 */
class JwLocator_QueryCache
{
    /**
     * Singleton Instance
     *
     * @var JwLocator_QueryCache
     */
    static private $instance;

    /**
     * Cached queries
     *
     * @var array
     */
    private $cacheItems = array();

    /**
     * Singleton loader
     *
     * @return JwLocator_QueryCache
     */
    public static function fetch()
    {
        if(!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
    }

    /**
     * Store data in memory cache
     *
     * @param string $key
     * @param $result
     */
    public function set($key, $result)
    {
        $this->cacheItems[$key] = $result;
    }

    /**
     * Check for existence of cache data
     *
     * @param string $key
     * @return bool
     */
    public function has($key) {
        return isset($this->cacheItems[$key]);
    }

    /**
     * Fetch memory data
     *
     * @param string $key
     * @return mixed
     */
    public function get($key) {
        PerchUtil::debug('Fetching query "'. $key .'" from cache');
        return $this->cacheItems[$key];
    }
};
