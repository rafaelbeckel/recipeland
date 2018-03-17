<?php

declare(strict_types=1);

namespace Recipeland\Helpers;

use Monolog\Logger;
use Recipeland\Interfaces\ScreamInterface;

/**
 * This class is just an alias to the Dependency Injection container.
 *
 * A Screamer logger is supposed to log AND send an e-mail, SMS, or
 * some notification to the developers in the case of severe errors
 */
class Screamer extends Logger implements ScreamInterface
{
}
