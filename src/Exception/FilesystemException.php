<?php
declare(strict_types=1);

namespace Jentry\Cache\Exception;

use Psr\SimpleCache\InvalidArgumentException;

class FilesystemException extends \Exception implements InvalidArgumentException
{

}