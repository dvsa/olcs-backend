<?php

namespace Dvsa\Olcs\Api\Domain\Repository\Factory;

use Dvsa\Olcs\Api\Domain\Repository\CompaniesHouseVsOlcsDiffs;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

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
    public function createService(ServiceLocatorInterface $sm): CompaniesHouseVsOlcsDiffs
    {
        return $this->__invoke($sm, CompaniesHouseVsOlcsDiffs::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return CompaniesHouseVsOlcsDiffs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CompaniesHouseVsOlcsDiffs
    {
        $sl = $container->getServiceLocator();
        return new CompaniesHouseVsOlcsDiffs(
            $sl->get('doctrine.connection.export')
        );
    }
}
