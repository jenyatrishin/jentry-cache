<?php
declare(strict_types=1);

namespace Jentry\Cache\Exception;

use Psr\SimpleCache\InvalidArgumentException;

class CacheInvalidArgumentException extends \Exception implements InvalidArgumentException
{

}