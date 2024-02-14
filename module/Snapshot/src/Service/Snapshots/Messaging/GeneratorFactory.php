<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\Messaging;

use Dvsa\Olcs\Snapshot\Service\Snapshots\AbstractGeneratorServices;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class GeneratorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new Generator(
            $container->get(AbstractGeneratorServices::class),
        );
    }
}
