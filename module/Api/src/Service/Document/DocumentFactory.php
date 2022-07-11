<?php

namespace Dvsa\Olcs\Api\Service\Document;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class DocumentFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new Document(
            $container->get('DateService'),
            $container->get('ContentStore'),
            $container->get('translator')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return Document
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return $this($services, Document::class);
    }
}
