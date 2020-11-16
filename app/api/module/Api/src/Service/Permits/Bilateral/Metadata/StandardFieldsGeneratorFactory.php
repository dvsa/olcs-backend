<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Metadata;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class StandardFieldsGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return StandardFieldsGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new StandardFieldsGenerator(
            $serviceLocator->get('PermitsBilateralMetadataCurrentFieldValuesGenerator')
        );
    }
}
