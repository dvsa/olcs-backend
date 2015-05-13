<?php

/**
 * Abstract Query Handler
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\Repository\RepositoryInterface;

/**
 * Abstract Query Handler
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractQueryHandler implements QueryHandlerInterface, FactoryInterface
{
    /**
     * @var RepositoryInterface
     */
    private $repo;

    /**
     * @var QueryHandlerInterface
     */
    private $queryHandler;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->repo = $serviceLocator->getServiceLocator()->get('RepositoryServiceManager')->get('Application');
        $this->queryHandler = $serviceLocator;

        return $this;
    }

    /**
     * @return RepositoryInterface
     */
    protected function getRepo()
    {
        return $this->repo;
    }

    /**
     * @return QueryHandlerInterface
     */
    protected function getQueryHandler()
    {
        return $this->queryHandler;
    }
}
