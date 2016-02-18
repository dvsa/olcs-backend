<?php

namespace Dvsa\Olcs\Api\Service\Nr\InputFilter;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Service\InputFilter\Input;
use Dvsa\Olcs\Api\Service\Nr\Filter\Format\SiDates as SiDateFilter;
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
        $service = new Input('serious_infringement');

        $filterChain = $service->getFilterChain();
        $filterChain->attach($serviceLocator->get('FilterManager')->get(SiDateFilter::class));

        $validatorChain = $service->getValidatorChain();
        $validatorChain->attach($serviceLocator->get('ValidatorManager')->get(ImposedDateValidator::class));

        return $service;
    }
}
