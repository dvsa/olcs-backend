<?php

namespace Dvsa\Olcs\Api\Domain\Repository\Factory;

use Dvsa\Olcs\Api\Domain\Repository\CompaniesHouseVsOlcsDiffs;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
        return $this($sm, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new CompaniesHouseVsOlcsDiffs(
            $container->get('doctrine.connection.export')
        );
    }
}
