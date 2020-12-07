<?php

namespace Dvsa\Olcs\Api\Domain\Repository\Factory;

use Dvsa\Olcs\Api\Domain\Repository\DataDvaNi;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

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
        $sl = $sm->getServiceLocator();

        return new DataDvaNi(
            $sl->get('doctrine.connection.export')
        );
    }
}
