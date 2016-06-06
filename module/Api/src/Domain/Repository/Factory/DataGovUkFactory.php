<?php

namespace Dvsa\Olcs\Api\Domain\Repository\Factory;

use Dvsa\Olcs\Api\Domain\Repository\DataGovUk;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
    public function createService(ServiceLocatorInterface $sm)
    {
        $sl = $sm->getServiceLocator();

        return new DataGovUk(
            $sl->get('doctrine.connection.export')
        );
    }
}
