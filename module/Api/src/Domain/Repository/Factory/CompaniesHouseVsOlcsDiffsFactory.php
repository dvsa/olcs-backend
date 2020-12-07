<?php

namespace Dvsa\Olcs\Api\Domain\Repository\Factory;

use Dvsa\Olcs\Api\Domain\Repository\CompaniesHouseVsOlcsDiffs;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for @see Dvsa\Olcs\Api\Domain\Repository\CompanyHouseVsOlcsDiffs
 *
 * @author Dmitry Golubev <dmitrij.golubev@valtech.co.uk>
 */
class CompaniesHouseVsOlcsDiffsFactory implements FactoryInterface
{
    /**
     * Create a repository
     *
     * @param \Dvsa\Olcs\Api\Domain\RepositoryServiceManager $sm Repos Manager
     *
     * @return CompaniesHouseVsOlcsDiffs
     */
    public function createService(ServiceLocatorInterface $sm)
    {
        $sl = $sm->getServiceLocator();

        return new CompaniesHouseVsOlcsDiffs(
            $sl->get('doctrine.connection.export')
        );
    }
}
