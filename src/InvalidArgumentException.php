<?php

namespace Src;

use Psr\SimpleCache\InvalidArgumentException as InvalidArgumentExceptionInterface;

class InvalidArgumentException extends \Exception implements InvalidArgumentExceptionInterface
{

}
