<?php

namespace Src\Exceptions;

use Psr\SimpleCache\InvalidArgumentException as InvalidArgumentExceptionInterface;

class InvalidArgumentException extends \Exception implements InvalidArgumentExceptionInterface
{

}
