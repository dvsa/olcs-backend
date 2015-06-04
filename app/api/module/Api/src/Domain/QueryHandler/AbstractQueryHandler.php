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
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;

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

    protected $repoServiceName;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        if ($this instanceof AuthAwareInterface) {
            $this->setAuthService($mainServiceLocator->get(AuthorizationService::class));
        }

        if ($this->repoServiceName === null) {
            throw new RuntimeException('The repoServiceName property must be define in a CommandHandler');
        }

        $this->repo = $mainServiceLocator->get('RepositoryServiceManager')->get($this->repoServiceName);

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
