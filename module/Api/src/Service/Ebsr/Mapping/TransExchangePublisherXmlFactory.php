<?php

namespace Dvsa\Olcs\Api\Service\Ebsr\Mapping;

use Olcs\XmlTools\Xml\Specification\MultiNodeValue;
use Olcs\XmlTools\Xml\Specification\NodeValue;
use Olcs\XmlTools\Xml\Specification\Recursion;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

/**
 * Class TransExchangePublisherXmlFactory
 * @package Olcs\Ebsr\Data\Mapping
 */
class TransExchangePublisherXmlFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator): Recursion
    {
        return $this->__invoke($serviceLocator, Recursion::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return Recursion
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): Recursion
    {
        $publisherResponse = [
            'BadRequest' => new NodeValue('error'),
            'Failed' => new NodeValue('error'),
            'Completed' => new Recursion('OutputFiles', new Recursion('OutputFile', new MultiNodeValue('files')))
        ];
        return new Recursion('PublisherResponse', new Recursion($publisherResponse));
    }
}
