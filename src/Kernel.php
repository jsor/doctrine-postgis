<?php

namespace Jsor\Doctrine\PostGIS;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        $bundles = require $this->getProjectDir() . '/config/bundles.php';

        if (!isset($bundles[JsorDoctrinePostgisBundle::class])) {
            $bundles[JsorDoctrinePostgisBundle::class] = ['all' => true];
        }

        foreach ($bundles as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }
    }
}
