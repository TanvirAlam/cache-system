<?php

namespace Src\Interfaces;

interface CacheItemInterface
{
    public function isHit();

    public function get();

    public function set($ttl);
}