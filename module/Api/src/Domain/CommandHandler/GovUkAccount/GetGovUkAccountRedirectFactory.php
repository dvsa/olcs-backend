<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\GovUkAccount;

use Dvsa\Olcs\Api\Domain\CommandHandler\TransactioningCommandHandler;
use Dvsa\Olcs\Api\Service\GovUkAccount\GovUkAccountService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class GetGovUkAccountRedirectFactory implements FactoryInterface
{
    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return GetGovUkAccountRedirect
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): GetGovUkAccountRedirect
    {
        $sl = $container->getServiceLocator();
        return (new GetGovUkAccountRedirect($sl->get(GovUkAccountService::class)))->createService($container);
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return TransactioningCommandHandler
     * @deprecated Use __invoke
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null): GetGovUkAccountRedirect
    {
        return $this->__invoke($serviceLocator, null);
    }
}
