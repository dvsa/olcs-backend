<?php

namespace Dvsa\Olcs\Api\Service\Ebsr\InputFilter;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Service\InputFilter\Input;
use Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ShortNotice\MissingSection;
use Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ShortNotice\MissingReason;
use Interop\Container\ContainerInterface;

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
        $inputName = 'short_notice';
        $service = new Input($inputName);
        $config = $container->get('Config');
        $validatorChain = $service->getValidatorChain();
        //allows validators to be switched off (debug only, not to be used for production)
        if (!isset($config['ebsr']['validate'][$inputName]) || $config['ebsr']['validate'][$inputName] === true) {
            /** @var ServiceLocatorInterface $validatorManager */
            $validatorManager = $container->get('ValidatorManager');
            $validatorChain->attach($validatorManager->get(MissingSection::class), true);
            $validatorChain->attach($validatorManager->get(MissingReason::class));
        }
        return $service;
    }
}
