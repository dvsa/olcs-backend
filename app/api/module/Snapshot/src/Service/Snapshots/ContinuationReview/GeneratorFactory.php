<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ContinuationReview;

use Dvsa\Olcs\Snapshot\Service\Snapshots\AbstractGeneratorServices;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class GeneratorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new Generator(
            $container->get(AbstractGeneratorServices::class),
            $container->get('SectionAccessService'),
            $container->get('Utils\NiTextTranslation'),
            $container
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return Generator
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return $this($services, Generator::class);
    }
}
