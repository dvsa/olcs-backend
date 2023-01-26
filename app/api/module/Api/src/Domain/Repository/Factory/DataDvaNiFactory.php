<?php

namespace Dvsa\Olcs\Api\Domain\Repository\Factory;

use Dvsa\Olcs\Api\Domain\Repository\DataDvaNi;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

/**
 * Factory for @see Dvsa\Olcs\Api\Domain\Repository\DataDvaNi
 *
 */
class DataDvaNiFactory implements FactoryInterface
{
    /**
     * @param \Dvsa\Olcs\Api\Domain\RepositoryServiceManager $sm the Service Manager
     *
     * @return DataDvaNi
     */
    public function createService(ServiceLocatorInterface $sm): DataDvaNi
    {
        return $this->__invoke($sm, DataDvaNi::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return DataDvaNi
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): DataDvaNi
    {
        $sl = $container->getServiceLocator();
        return new DataDvaNi(
            $sl->get('doctrine.connection.export')
        );
    }
}
