<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Date;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class DateGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return DateGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new DateGenerator(
            $serviceLocator->get('QaDateElementFactory')
        );
    }
}
