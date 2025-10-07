<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Event;

use Doctrine\Deprecations\Deprecation;
use Doctrine\ORM\Tools\ToolEvents;

/**
 * @deprecated Use Jsor\Doctrine\PostGIS\Event\ORMSchemaEventListener instead. This class will be removed in 3.0.
 *
 * @psalm-suppress all
 */
class ORMSchemaEventSubscriber extends DBALSchemaEventSubscriber
{
    use ORMSchemaEventSubscriberCompatibilityTrait;

    public function __construct()
    {
        parent::__construct();

        Deprecation::trigger(
            'jsor/doctrine-postgis',
            'https://github.com/jsor/doctrine-postgis/pull/61',
            'Since 2.4: The "%s" class is deprecated and will be removed in 3.0. Use "%s" instead.',
            self::class,
            ORMSchemaEventListener::class
        );
    }

    public function getSubscribedEvents(): array
    {
        return array_merge(
            parent::getSubscribedEvents(),
            [
                ToolEvents::postGenerateSchemaTable,
            ]
        );
    }
}
