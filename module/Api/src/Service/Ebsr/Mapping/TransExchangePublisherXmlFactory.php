<?php

namespace Dvsa\Olcs\Api\Service\Ebsr\Mapping;

use Interop\Container\ContainerInterface;
use Olcs\XmlTools\Xml\Specification\MultiNodeValue;
use Olcs\XmlTools\Xml\Specification\NodeValue;
use Olcs\XmlTools\Xml\Specification\Recursion;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $publisherResponse = [
            'BadRequest' => new NodeValue('error'),
            'Failed' => new NodeValue('error'),
            'Completed' => new Recursion('OutputFiles', new Recursion('OutputFile', new MultiNodeValue('files')))
        ];

        return new Recursion('PublisherResponse', new Recursion($publisherResponse));
    }
}
