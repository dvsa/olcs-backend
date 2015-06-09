<?php

/**
 * Abstract Command Handler
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler;

use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\Repository\RepositoryInterface;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\QueryHandler\QueryHandlerInterface;

/**
 * Abstract Command Handler
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractCommandHandler implements CommandHandlerInterface, FactoryInterface
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
     * @var CommandHandlerInterface
     */
    private $commandHandler;

    /**
     * @var QueryHandlerInterface
     */
    private $queryHandler;

    private $repoManager;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var ServiceLocatorInterface $mainServiceLocator  */
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        if ($this instanceof AuthAwareInterface) {
            $this->setAuthService($mainServiceLocator->get(AuthorizationService::class));
        }

        if ($this->repoServiceName === null) {
            throw new RuntimeException('The repoServiceName property must be define in a CommandHandler');
        }

        $this->repoManager = $mainServiceLocator->get('RepositoryServiceManager');

        $this->extraRepos[] = $this->repoServiceName;

        $this->commandHandler = $serviceLocator;

        $this->queryHandler = $mainServiceLocator->get('QueryHandlerManager');

        if ($this instanceof TransactionedInterface) {
            return new TransactioningCommandHandler($this, $mainServiceLocator->get('TransactionManager'));
        }

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
     * @return CommandHandlerInterface
     */
    protected function getCommandHandler()
    {
        return $this->commandHandler;
    }

    /**
     * @return QueryHandlerInterface
     */
    protected function getQueryHandler()
    {
        return $this->queryHandler;
    }
}
