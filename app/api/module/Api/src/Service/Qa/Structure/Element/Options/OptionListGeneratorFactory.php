<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Options;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class OptionListGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return OptionListGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $optionListGenerator = new OptionListGenerator(
            $serviceLocator->get('QaOptionListFactory'),
            $serviceLocator->get('QaOptionFactory')
        );

        $optionListGenerator->registerSource('refData', $serviceLocator->get('QaRefDataOptionsSource'));
        $optionListGenerator->registerSource(
            'ecmtPermitUsageThreeOptionsRefData',
            $serviceLocator->get('QaEcmtPermitUsageThreeOptionsRefDataOptionsSource')
        );
        $optionListGenerator->registerSource(
            'ecmtPermitUsageFourOptionsRefData',
            $serviceLocator->get('QaEcmtPermitUsageFourOptionsRefDataOptionsSource')
        );
        $optionListGenerator->registerSource('repoQuery', $serviceLocator->get('QaRepoQueryOptionsSource'));

        return $optionListGenerator;
    }
}
