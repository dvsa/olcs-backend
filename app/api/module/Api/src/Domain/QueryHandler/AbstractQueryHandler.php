<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler;

use Dvsa\Olcs\Api\Domain\HandlerEnabledTrait;
use Dvsa\Olcs\Api\Domain\RedisAwareInterface;
use Dvsa\Olcs\Api\Domain\UploaderAwareInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\Repository\RepositoryInterface;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\NationalRegisterAwareInterface;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareInterface;
use Dvsa\Olcs\Api\Domain\ToggleAwareInterface;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Service\OpenAm\UserInterface;
use Dvsa\Olcs\Api\Service\Toggle\ToggleService;
use Dvsa\Olcs\Transfer\Command\Audit as AuditCommand;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Entity;
use Olcs\Logging\Log\Logger;
use Zend\Cache\Storage\Adapter\Redis;
use Zend\ServiceManager\Exception\ExceptionInterface as ZendServiceException;

/**
 * Abstract Query Handler
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractQueryHandler implements QueryHandlerInterface, FactoryInterface, AuthAwareInterface
{
    use AuthAwareTrait;
    use HandlerEnabledTrait;

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
     * @var \Dvsa\Olcs\Api\Domain\QueryHandlerManager
     */
    private $queryHandler;

    private $repoManager;

    /**
     * @var \Dvsa\Olcs\Api\Domain\CommandHandlerManager
     */
    private $commandHandler;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator Service locator
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        try {
            $this->applyInterfaces($mainServiceLocator);
        } catch (ZendServiceException $e) {
            $this->logServiceExceptions($e);
        }

        $this->repoManager = $mainServiceLocator->get('RepositoryServiceManager');

        $this->extraRepos[] = $this->repoServiceName;

        $this->queryHandler = $serviceLocator;

        $this->commandHandler = $mainServiceLocator->get('CommandHandlerManager');

        return $this;
    }

    /**
     * Get the repository
     *
     * @param string $name Repository name
     *
     * @return RepositoryInterface
     *
     * @throws RuntimeException
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
     * Get query handler
     *
     * @return \Dvsa\Olcs\Api\Domain\QueryHandlerManager
     */
    protected function getQueryHandler()
    {
        return $this->queryHandler;
    }

    /**
     * Get result list
     *
     * @param mixed $objects objects
     * @param array $bundle  Result bundle to retrieve
     *
     * @return array
     */
    protected function resultList($objects, array $bundle = [])
    {
        return (new ResultList($objects, $bundle))->serialize();
    }

    /**
     * Create result object
     *
     * @param mixed $object Result object
     * @param array $bundle bundle array
     * @param array $values values
     *
     * @return Result
     */
    protected function result($object, array $bundle = [], $values = [])
    {
        return new Result($object, $bundle, $values);
    }

    /**
     * Create a read audit for an entity
     *
     * @param object $entity The entity which has been read
     *
     * @return void
     * @throws RuntimeException
     */
    protected function auditRead($entity)
    {
        if ($this->isAnonymousUser() || !$this->isInternalUser()) {
            // if not an internal user then do nothing
            return;
        }

        $data = ['id' => $entity->getId()];

        // instanceof allows unit tests to mock
        switch (true) {
            case $entity instanceof Entity\Organisation\Organisation:
                $dto = AuditCommand\ReadOrganisation::create($data);
                break;
            case $entity instanceof Entity\Licence\Licence:
                $dto = AuditCommand\ReadLicence::create($data);
                break;
            case $entity instanceof Entity\Cases\Cases:
                $dto = AuditCommand\ReadCase::create($data);
                break;
            case $entity instanceof Entity\Application\Application:
                $dto = AuditCommand\ReadApplication::create($data);
                break;
            case $entity instanceof Entity\Bus\BusReg:
                $dto = AuditCommand\ReadBusReg::create($data);
                break;
            case $entity instanceof Entity\Tm\TransportManager:
                $dto = AuditCommand\ReadTransportManager::create($data);
                break;
            case $entity instanceof Entity\Permits\IrhpApplication:
                $dto = AuditCommand\ReadIrhpApplication::create($data);
                break;
            default:
                throw new \RuntimeException('Cannot create audit read for entity, no DTO is defined');
        }

        $this->commandHandler->handleCommand($dto);
    }

    /**
     * Get command handler
     *
     * @return \Dvsa\Olcs\Api\Domain\CommandHandlerManager
     */
    protected function getCommandHandler()
    {
        return $this->commandHandler;
    }

    /**
     * Warnings suppressed as by design this is just a series of 'if' conditions
     *
     * @param ServiceLocatorInterface $mainServiceLocator service locator
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function applyInterfaces($mainServiceLocator)
    {
        if ($this instanceof ToggleRequiredInterface || $this instanceof ToggleAwareInterface) {
            $toggleService = $mainServiceLocator->get(ToggleService::class);
            $this->setToggleService($toggleService);
        }

        if ($this instanceof AuthAwareInterface) {
            $this->setAuthService($mainServiceLocator->get(AuthorizationService::class));
        }

        if ($this instanceof UploaderAwareInterface) {
            $this->setUploader($mainServiceLocator->get('FileUploader'));
        }

        if ($this instanceof \Dvsa\Olcs\Api\Domain\CpmsAwareInterface) {
            $this->setCpmsService($mainServiceLocator->get('CpmsHelperService'));
        }

        if ($this instanceof \Dvsa\Olcs\Api\Domain\CompaniesHouseAwareInterface) {
            $this->setCompaniesHouseService($mainServiceLocator->get('CompaniesHouseService'));
        }

        if ($this instanceof \Dvsa\Olcs\Address\Service\AddressServiceAwareInterface) {
            $this->setAddressService($mainServiceLocator->get('AddressService'));
        }

        if ($this instanceof NationalRegisterAwareInterface) {
            $this->setNationalRegisterConfig($mainServiceLocator->get('Config')['nr']);
        }

        if ($this instanceof OpenAmUserAwareInterface) {
            $this->setOpenAmUser($mainServiceLocator->get(UserInterface::class));
        }

        if ($this instanceof RedisAwareInterface) {
            /** @var Redis $redis */
            $redis = $mainServiceLocator->get(Redis::class);
            $this->setRedis($redis);
        }
    }

    /**
     * Zend ServiceManager masks exceptions behind a simple 'service not created'
     * message so here we inspect the 'previous exception' chain and log out
     * what the actual errors were, before rethrowing the original execption.
     *
     * @param \Exception $e exception
     *
     * @return void
     * @throws \Exception rethrows original Exception
     */
    private function logServiceExceptions(\Exception $e)
    {
        $rethrow = $e;

        do {
            Logger::warn(get_class($this) . ': ' . $e->getMessage());
            $e = $e->getPrevious();
        } while ($e);

        throw $rethrow;
    }
}
