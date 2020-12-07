<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Metadata;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

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
