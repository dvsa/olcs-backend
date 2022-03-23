<?php
declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\User;

use Dvsa\Olcs\Api\Domain\CommandHandler\TransactioningCommandHandler;
use Dvsa\Olcs\Api\Service\EventHistory\Creator;
use Dvsa\Olcs\Auth\Service\PasswordService;
use Interop\Container\ContainerInterface;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class UpdateUserSelfserveFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     *
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): TransactioningCommandHandler
    {
        $sl = $container->getServiceLocator();
        $authAdapter = $sl->get(ValidatableAdapterInterface::class);
        $passwordService = $sl->get(PasswordService::class);
        $eventHistoryCreator = $sl->get(Creator::class);

        return (new UpdateUserSelfserve($authAdapter, $passwordService, $eventHistoryCreator))->createService($container);
    }

    /**
     * @deprecated Remove once Laminas v3 upgrade is complete
     */
    public function createService(ServiceLocatorInterface $serviceLocator): TransactioningCommandHandler
    {
        return $this($serviceLocator, UpdateUserSelfserve::class);
    }
}
