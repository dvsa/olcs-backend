<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\TransportManagerApplication;

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
            $container->get('Utils\NiTextTranslation'),
            $container->get('Review\TransportManagerMain'),
            $container->get('Review\TransportManagerResponsibility'),
            $container->get('Review\TransportManagerOtherEmployment'),
            $container->get('Review\TransportManagerPreviousConviction'),
            $container->get('Review\TransportManagerPreviousLicence'),
            $container->get('Review\TransportManagerDeclaration'),
            $container->get('Review\TransportManagerSignature')
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
