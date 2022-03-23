<?php

declare(strict_types = 1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Auth;

use Interop\Container\ContainerInterface;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ResetPasswordOpenAmFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return ResetPasswordOpenAm
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ResetPasswordOpenAm
    {
        $sl = $container->getServiceLocator();
        $adapter = $sl->get(ValidatableAdapterInterface::class);
        $eventHistoryCreator = $sl->get('EventHistoryCreator');
        $instance = new ResetPasswordOpenAm($adapter, $eventHistoryCreator);
        return $instance->createService($container);
    }

    /**
     * @deprecated can be removed following laminas v3 upgrade
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ResetPasswordOpenAm
     */
    public function createService(ServiceLocatorInterface $serviceLocator): ResetPasswordOpenAm
    {
        return $this->__invoke($serviceLocator, ResetPasswordOpenAm::class);
    }
}
