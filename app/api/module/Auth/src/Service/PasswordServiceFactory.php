<?php
declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Service;

use Faker\Factory;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class PasswordServiceFactory implements FactoryInterface
{
    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return PasswordService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): PasswordService
    {
        return new PasswordService(Factory::create());
    }

    /**
     * @inheritDoc
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): PasswordService
    {
        return $this->__invoke($serviceLocator, null);
    }
}
