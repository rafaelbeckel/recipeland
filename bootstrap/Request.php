<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\ServerRequest as Request;

return Request::fromGlobals();
