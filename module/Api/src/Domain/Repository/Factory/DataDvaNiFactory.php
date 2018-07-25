<?php

namespace Dvsa\Olcs\Api\Domain\Repository\Factory;

use Dvsa\Olcs\Api\Domain\Repository\DataDvaNi;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
    public function createService(ServiceLocatorInterface $sm)
    {
        return $this($sm, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new DataDvaNi(
            $container->get('doctrine.connection.export')
        );
    }
}
