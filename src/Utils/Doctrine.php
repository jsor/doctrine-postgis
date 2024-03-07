<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Utils;

use Doctrine\DBAL\Connection\StaticServerVersionProvider;

final class Doctrine
{
    public static function isV3(): bool
    {
        return !class_exists(StaticServerVersionProvider::class);
    }
}
