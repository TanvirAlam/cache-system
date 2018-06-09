<?php

namespace Src\Drivers;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Src\Exceptions\InvalidArgumentException;

class FileCache implements CacheItemPoolInterface
{
    /**
     * @var CacheItemInterface[]
     */
    private $deferredStack = [];

    /**
     * Returns a Cache Item representing the specified key.
     *
     * @param string $key
     *
     * @throws InvalidArgumentException
     * 
     * @return CacheItemInterface
     */
    public function getItem($key)
    {
        $this->assertValidKey($key);

        if (isset($this->deferredStack[$key])) {
            return clone $this->deferredStack[$key];
        }

        $file = @file_get_contents($this->filenameFor($key));

        if (false !== $file) {
            return unserialize($file);
        }

        return new Item($key);
    }

    /**
     * Returns a traversable set of cache items.
     *
     * @param array $keys
     *
     * @throws InvalidArgumentException
     *
     * @return array|\Traversable
     */
    public function getItems(array $keys = array())
    {
        $items = [];

        foreach ($keys as $key) {
            $items[$key] = $this->getItem($key);
        }

        return $items;
    }

    /**
     * Confirms if the cache contains specified cache item.
     *
     * Note: This method MAY avoid retrieving the cached value for performance reasons.
     * This could result in a race condition with CacheItemInterface::get(). To avoid
     * such situation use CacheItemInterface::isHit() instead.
     *
     * @param string $key
     *
     * @throws InvalidArgumentException
     *
     * @return bool
     */
    public function hasItem($key)
    {
        $this->assertValidKey($key);

        $itemInDeferredNotExpired = isset($this->deferredStack[$key]) && $this->deferredStack[$key]->isHit();

        return $itemInDeferredNotExpired || file_exists($this->filenameFor($key));
    }

    /**
     * Deletes all items in the pool.
     *
     * @return bool
     */
    public function clear()
    {
        $this->deferredStack = [];

        $result = true;
        foreach (glob($this->getFolder() . '/' . $this->getFilenamePrefix() . '*') as $filename) {
            $result = $result && unlink($filename);
        }

        return $result;
    }

    /**
     * Removes the item from the pool.
     *
     * @param string $key
     * 
     * @throws InvalidArgumentException
     *
     * @return bool
     */
    public function deleteItem($key)
    {
        $this->assertValidKey($key);

        if (isset($this->deferredStack[$key])) {
            unset($this->deferredStack[$key]);
        }

        @unlink($this->filenameFor($key));

        return true;
    }

    /**
     * Removes multiple items from the pool.
     *
     * @param array $keys
     *
     * @throws InvalidArgumentException
     *
     * @return bool
     */
    public function deleteItems(array $keys)
    {
        $result = true;

        foreach ($keys as $key) {
            $result = $result && $this->deleteItem($key);
        }

        return $result;
    }

    /**
     * Persists a cache item immediately.
     *
     * @param CacheItemInterface $item
     *
     * @return bool
     */
    public function save(CacheItemInterface $item)
    {
        if (!$item->isHit()) {
            return false;
        }

        $bytes = file_put_contents($this->filenameFor($item->getKey()), serialize($item));

        return (false !== $bytes);
    }

    /**
     * Sets a cache item to be persisted later.
     *
     * @param CacheItemInterface $item
     *   The cache item to save.
     *
     * @return bool
     *   False if the item could not be queued or if a commit was attempted and failed. True otherwise.
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        $this->deferredStack[$item->getKey()] = $item;

        return true;
    }

    /**
     * Persists any deferred cache items.
     *
     * @return bool
     *   True if all not-yet-saved items were successfully saved or there were none. False otherwise.
     */
    public function commit()
    {
        $result = true;

        foreach ($this->deferredStack as $key => $item) {
            $result = $result && $this->save($item);
            unset($this->deferredStack[$key]);
        }

        return $result;
    }

    private function filenameFor($key)
    {
        return $this->getFolder() . '/' . $this->getFilenamePrefix() . $key;
    }

    /**
     * Checks if a key is valid for APCu cache storage
     *
     * @param $key
     * 
     * @throws InvalidArgumentException
     */
    private function assertValidKey($key)
    {
        if (!Item::isValidKey($key)) {
            throw new InvalidArgumentException('invalid key: ' . var_export($key, true));
        }
    }

    /**
     * @return string
     */
    private function getFolder()
    {
        return sys_get_temp_dir();
    }

    /**
     * @return string
     */
    private function getFilenamePrefix()
    {
        return 'tumbleweed-cache-';
    }

    public function __destruct()
    {
        $this->commit();
    }
}
