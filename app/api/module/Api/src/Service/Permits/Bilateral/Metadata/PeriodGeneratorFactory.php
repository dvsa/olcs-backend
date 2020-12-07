<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Metadata;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class PeriodGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return PeriodGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $periodGenerator = new PeriodGenerator(
            $serviceLocator->get('RepositoryServiceManager')->get('IrhpPermitStock')
        );

        $periodGenerator->registerFieldsGenerator(
            Behaviour::STANDARD,
            $serviceLocator->get('PermitsBilateralMetadataStandardFieldsGenerator')
        );

        $periodGenerator->registerFieldsGenerator(
            Behaviour::MOROCCO,
            $serviceLocator->get('PermitsBilateralMetadataMoroccoFieldsGenerator')
        );

        return $periodGenerator;
    }
}
