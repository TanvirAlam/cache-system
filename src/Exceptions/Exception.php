<?php

namespace Src\Exceptions;

use Psr\Cache\CacheException;

class Exception extends \Exception implements CacheException
{

}
