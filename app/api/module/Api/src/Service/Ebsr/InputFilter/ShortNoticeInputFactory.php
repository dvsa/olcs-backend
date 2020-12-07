<?php

namespace Dvsa\Olcs\Api\Service\Ebsr\InputFilter;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Service\InputFilter\Input;
use Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ShortNotice\MissingSection;
use Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ShortNotice\MissingReason;

/**
 * Class ShortNoticeInputFactory
 * @package Dvsa\Olcs\Api\Service\Ebsr\InputFilter
 */
class ShortNoticeInputFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator service locator
     *
     * @return Input
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $inputName = 'short_notice';
        $service = new Input($inputName);
        $config = $serviceLocator->get('Config');

        $validatorChain = $service->getValidatorChain();

        //allows validators to be switched off (debug only, not to be used for production)
        if (!isset($config['ebsr']['validate'][$inputName]) || $config['ebsr']['validate'][$inputName] === true) {
            /** @var ServiceLocatorInterface $validatorManager */
            $validatorManager = $serviceLocator->get('ValidatorManager');
            $validatorChain->attach($validatorManager->get(MissingSection::class), true);
            $validatorChain->attach($validatorManager->get(MissingReason::class));
        }

        return $service;
    }
}
