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
        $inputName = 'short_notice';
        $service = new Input($inputName);
        $config = $serviceLocator->get('Config');

        $validatorChain = $service->getValidatorChain();

        //allows validators to be switched off (debug only, not to be used for production)
        if (!isset($config['ebsr']['validate'][$inputName]) || $config['ebsr']['validate'][$inputName] === true) {
            $validatorChain->attach($serviceLocator->get('ValidatorManager')->get('Rules\ShortNotice\MissingSection'));
            $validatorChain->attach($serviceLocator->get('ValidatorManager')->get('Rules\ShortNotice\MissingReason'));
        }

        return $service;
    }
}
