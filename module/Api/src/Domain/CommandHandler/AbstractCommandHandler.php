<?php

/**
 * Abstract Command Handler
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler;

use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\Repository\RepositoryInterface;
use ZfcRbac\Service\AuthorizationService;

/**
 * Abstract Command Handler
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractCommandHandler implements CommandHandlerInterface
{
    /**
     * @var RepositoryInterface
     */
    private $repo;

    /**
     * @var CommandHandlerInterface
     */
    private $commandHandler;

    /**
     * @var AuthorizationService
     */
    private $authService;

    protected $repoServiceName;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        // @todo we might not always need this, so we could optional fetch it from the sm
        $this->authService = $mainServiceLocator->get(AuthorizationService::class);

        if ($this->repoServiceName === null) {
            throw new RuntimeException('The repoServiceName property must be define in a CommandHandler');
        }

        $this->repo = $mainServiceLocator->get('RepositoryServiceManager')
            ->get($this->repoServiceName);

        $this->commandHandler = $serviceLocator;

        return $this;
    }

    protected function isGranted($permission)
    {
        return $this->authService->isGranted($permission);
    }

    /**
     * @return RepositoryInterface
     */
    protected function getRepo()
    {
        return $this->repo;
    }

    /**
     * @return CommandHandlerInterface
     */
    protected function getCommandHandler()
    {
        return $this->commandHandler;
    }
}
