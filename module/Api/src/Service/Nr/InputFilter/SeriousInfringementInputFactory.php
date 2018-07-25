<?php

namespace Dvsa\Olcs\Api\Service\Nr\InputFilter;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Service\InputFilter\Input;
use Dvsa\Olcs\Api\Service\Nr\Filter\Format\SiDates as SiDateFilter;
use Dvsa\Olcs\Api\Service\Nr\Filter\Format\IsExecuted;
use Dvsa\Olcs\Api\Service\Nr\Validator\SiPenaltyImposedDate as ImposedDateValidator;

/**
 * Class SeriousInfringementInputFactory
 * @package Dvsa\Olcs\Api\Service\Nr\InputFilter
 */
class SeriousInfringementInputFactory implements FactoryInterface
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
        $service = new Input('serious_infringement');

        $filterChain = $service->getFilterChain();
        $filterChain->attach($container->get('FilterManager')->get(IsExecuted::class));
        $filterChain->attach($container->get('FilterManager')->get(SiDateFilter::class));

        $validatorChain = $service->getValidatorChain();
        $validatorChain->attach($container->get('ValidatorManager')->get(ImposedDateValidator::class));

        return $service;
    }
}
