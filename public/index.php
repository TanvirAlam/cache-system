<?php

$cache = new Src\SimpleCache(new Doctrine\Common\Cache\FilesystemCache());
$value = $cache->get('foo');

if ($value === null) {
    $value = 123123123;

    $cache->set('foo', $value, 3600);
}

echo $value;