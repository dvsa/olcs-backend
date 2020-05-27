<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Metadata;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FieldsGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FieldsGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new FieldsGenerator(
            $serviceLocator->get('PermitsBilateralMetadataCurrentFieldValuesGenerator')
        );
    }
}
