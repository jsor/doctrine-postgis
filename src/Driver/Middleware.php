<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Driver;

use Doctrine\DBAL;

final class Middleware implements DBAL\Driver\Middleware
{
    public function wrap(DBAL\Driver $driver): DBAL\Driver
    {
        return new Driver($driver);
    }
}
