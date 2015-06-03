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
     * The name of the default repo
     */
    protected $repoServiceName;

    /**
     * Tell the factory which repositories to lazy load
     */
    protected $extraRepos = [];

    /**
     * Store the instantiated repos
     *
     * @var RepositoryInterface[]
     */
    private $repos = [];

    /**
     * @var QueryHandlerInterface
     */
    private $queryHandler;

    private $repoManager;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        if ($this instanceof AuthAwareInterface) {
            $this->setAuthService($mainServiceLocator->get(AuthorizationService::class));
        }

        if ($this->repoServiceName === null) {
            throw new RuntimeException('The repoServiceName property must be define in a CommandHandler');
        }

        $this->repoManager = $mainServiceLocator->get('RepositoryServiceManager');

        $this->extraRepos[] = $this->repoServiceName;

        $this->queryHandler = $serviceLocator;

        return $this;
    }

    /**
     * @return RepositoryInterface
     */
    protected function getRepo($name = null)
    {
        if ($name === null) {
            $name = $this->repoServiceName;
        }

        if (!in_array($name, $this->extraRepos)) {
            throw new RuntimeException('You have not injected the ' . $name . ' repository');
        }

        // Lazy load repository
        if (!isset($this->repos[$name])) {
            $this->repos[$name] = $this->repoManager->get($name);
        }

        return $this->repos[$name];
    }

    /**
     * @return QueryHandlerInterface
     */
    protected function getQueryHandler()
    {
        return $this->queryHandler;
    }
}
