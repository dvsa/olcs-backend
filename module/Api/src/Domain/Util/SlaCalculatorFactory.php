<?php

namespace Dvsa\Olcs\Api\Domain\Util;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class SlaCalculator
 * @package Dvsa\Olcs\Api\Domain\Util
 */
final class SlaCalculatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new SlaCalculator($serviceLocator->get(TimeProcessorBuilderInterface::class));
    }
}
