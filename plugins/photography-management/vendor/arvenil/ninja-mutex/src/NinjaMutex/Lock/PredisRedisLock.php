<?php
namespace NinjaMutex\Lock;

use \Predis;

/**
 * Lock implementor using Predis (client library for Redis)
 *
 * @author Kamil Dziedzic <arvenil@klecza.pl>
 */
class PredisRedisLock extends LockAbstract
{
    /**
     * Predis connection
     *
     * @var
     */
    protected $client;

    /**
     * @param $client Predis\Client
     */
    public function __construct($client)
    {
        parent::__construct();

        $this->client = $client;
    }

    /**
     * @param  string $name
     * @param  bool   $blocking
     * @return bool
     */
    protected function getLock($name, $blocking)
    {
        if (!$this->client->setnx($name, serialize($this->getLockInformation()))) {
            return false;
        }

        return true;
    }

    /**
     * Release lock
     *
     * @param  string $name name of lock
     * @return bool
     */
    public function releaseLock($name)
    {
        if (isset($this->locks[$name]) && $this->client->del($name)) {
            unset($this->locks[$name]);

            return true;
        }

        return false;
    }

    /**
     * Check if lock is locked
     *
     * @param  string $name name of lock
     * @return bool
     */
    public function isLocked($name)
    {
        return null !== $this->client->get($name);
    }
}
