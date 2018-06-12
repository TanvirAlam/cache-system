<?php

namespace Src;

use Doctrine\Common\Cache\Cache;
use Src\Interfaces\CacheInterface;

class SimpleCache implements CacheInterface
{
    /**
     * @var string
     * @var mixed
     * @var boolean
     * @var integer
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $key, $value, $isHit, $timeToLive, $handler;

    /**
     * Create a new controller instance. 
     */
    public function __construct(
        Cache $handler,
        $key,
        $value,
        $isHit,
        $timeToLive = 0
    ) {
        $this->handler = $handler;
        $this->key = $key;
        $this->value = $value;
        $this->isHit = $isHit;
        $this->timeToLive = $timeToLive;
    }

    /**
     * Fetch value through cache key
     *
     * @param String $key
     * @param null $default
     * 
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $value = $this->handler->fetch($key);
        return $value !== false ? $value : $default;
    }

    /**
     * Set value for cache using key
     *
     * @param String $key
     * @param mixed $value
     * @param Date $timeToLive
     * 
     * @return void
     */
    public function set($key, $value, $timeToLive = null)
    {
        return $this->handler->save(
            $key,
            $value,
            $timeToLive === null ? 0 : $timeToLive
        );
    }

    /**
     * Check if key exist
     *
     * @param String $key
     * 
     * @return boolean
     */
    public function has($key)
    {
        return $this->handler->contains($key);
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isHit()
    {
        return $this->isHit;
    }

    /**
     * Deletes cache key
     *
     * @param string $key
     * 
     * @return void
     */
    public function delete($key)
    {
        return $this->handler->delete($key);
    }

    /**
     * Clear the cache data
     *
     * @return void
     */
    public function clear()
    {
        return $this->handler->deleteAll();
    }

    /**
     * Set multiple cache array
     *
     * @param $value
     * @param int $timeToLive
     * 
     * @return void
     */
    public function setMultiple($values, $timeToLive = null)
    {
        return $this->handler->saveMultiple(
            $this->_getAsArray($values),
            $timeToLive
        );
    }

    /**
     * Get multiple cache array
     *
     * @param String $keys
     * @param array $default
     * 
     * @return array
     */
    public function getMultiple($keys, $default = null)
    {
        $result = [];
        $data = $this->handler->fetchMultiple(
            $this->_getAsArray($keys)
        );

        foreach ($keys as $key) {
            isset($data[$key])
                ? $result[$key] = $data[$key]
                : $result[$key] = $default;
        }

        return $result;
    }

    /**
     * Undocumented function
     *
     * @param string $keys
     * 
     * @return void
     * 
     * @throws InvalidArgumentException
     */
    private function _getAsArray($keys)
    {
        if (!is_array($keys)) {
            return new InvalidArgumentException('Value must be an array');
        }
        return $keys;
    }

    /**
     * Get the time for the expiration
     *
     * @return Date
     */
    public function getTimeToLive()
    {
        return $this->timeToLive;
    }

    /**
     * Remove multiple cache
     *
     * @param String $keys
     * 
     * @return void
     */
    public function deleteMultiple($keys)
    {
        $keys = $this->_getAsArray($keys);
        $result = true;
        foreach ($keys as $key) {
            $result = $this->handler->delete($key);
        }
        return $result;
    }

    /**
     * Sets the expiration date at a specifik time
     *
     * @param Integer $expiration
     * 
     * @return Date
     * 
     * @throws Exception
     */
    public function expiresAt($expiration)
    {
        if (is_int($expiration)) {
            $this->timeToLive = $expiration - time();
        } elseif ($expiration instanceof \DateTime) {
            $this->timeToLive = $expiration->getTimestamp() - time();
        } elseif ($expiration === null) {
            $this->timeToLive = 0;
        } else {
            throw new \Exception('Invalid expires at parameter');
        }
        return $this;
    }

    /**
     * Sets the expiration date after a specifik time
     *
     * @param Integer $expiration
     * 
     * @return Date
     * 
     * @throws Exception
     */
    public function expiresAfter($time)
    {
        if (is_int($time)) {
            $this->timeToLive = $time;
        } elseif ($time instanceof \DateInterval) {
            $now = new \DateTime();
            $now->add($time);
            $this->timeToLive = $now->getTimestamp() - time();
        } elseif ($time === null) {
            $this->timeToLive = 0;
        } else {
            throw new \Exception('Invalid expires after parameter');
        }
        return $this;
    }
}
