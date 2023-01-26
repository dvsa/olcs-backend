<?php

namespace Dvsa\Olcs\Api\Domain\Repository\Factory;

use Dvsa\Olcs\Api\Domain\Repository\DataGovUk;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

/**
 * Factory for @see Dvsa\Olcs\Api\Domain\Repository\DataGovUk
 *
 * @author Dmitry Golubev <dmitrij.golubev@valtech.co.uk>
 */
class DataGovUkFactory implements FactoryInterface
{
    /**
     * @param \Dvsa\Olcs\Api\Domain\RepositoryServiceManager $sm
     *
     * @return DataGovUk
     */
    public function createService(ServiceLocatorInterface $sm): DataGovUk
    {
        return $this->__invoke($sm, DataGovUk::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return DataGovUk
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): DataGovUk
    {
        $sl = $container->getServiceLocator();
        return new DataGovUk(
            $sl->get('doctrine.connection.export')
        );
    }
}
