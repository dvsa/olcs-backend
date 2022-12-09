<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\DigitalSignature;

use Dvsa\Olcs\Api\Domain\CommandHandler\TransactioningCommandHandler;
use Dvsa\Olcs\Api\Service\EventHistory\Creator;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class UpdateSurrenderFactory implements FactoryInterface
{
    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return TransactioningCommandHandler
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): TransactioningCommandHandler
    {
        $sl = $container->getServiceLocator();
        $eventHistoryCreator = $sl->get(Creator::class);

        return (new UpdateSurrender($eventHistoryCreator))->createService($container);
    }

    /**
     * @deprecated Remove once Laminas v3 upgrade is complete
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null): TransactioningCommandHandler
    {
        return $this($serviceLocator, UpdateSurrender::class);
    }
}
