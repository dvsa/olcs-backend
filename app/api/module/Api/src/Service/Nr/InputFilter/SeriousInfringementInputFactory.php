<?php

namespace Dvsa\Olcs\Api\Service\Nr\InputFilter;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Service\InputFilter\Input;
use Dvsa\Olcs\Api\Service\Nr\Filter\Format\SiDates as SiDateFilter;
use Dvsa\Olcs\Api\Service\Nr\Filter\Format\IsExecuted;
use Dvsa\Olcs\Api\Service\Nr\Validator\SiPenaltyImposedDate as ImposedDateValidator;
use Interop\Container\ContainerInterface;

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
    public function createService(ServiceLocatorInterface $serviceLocator): Input
    {
        return $this->__invoke($serviceLocator, Input::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return Input
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): Input
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
