<?php

namespace Dvsa\Olcs\Api\Service\Ebsr\InputFilter;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ShortNoticeInputFactory
 * @package Dvsa\Olcs\Api\Service\Ebsr\InputFilter
 */
class ShortNoticeInputFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $service = new Input('short_notice');

        $validatorChain = $service->getValidatorChain();
        $validatorChain->attach($serviceLocator->get('ValidatorManager')->get('Rules\ShortNotice\MissingSection'));
        $validatorChain->attach($serviceLocator->get('ValidatorManager')->get('Rules\ShortNotice\MissingReason'));

        return $service;
    }
}
