<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require $_SERVER['DOCUMENT_ROOT'] . '/src/SimpleCache.php';

class CacheTest
{
    public $cache;

    public function __construct()
    {
        $this->cache = new SimpleCache(new ArrayCache(), 'key', null, false);
    }

    public function show()
    {
        $this->cache->set('key', 'foobar');
        $value = $this->cache->get('key');

        return $value;
    }
}

echo (new CacheTest)->show();
