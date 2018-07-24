<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler;

use Dvsa\Olcs\Api\Domain\UploaderAwareInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\Repository\RepositoryInterface;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\NationalRegisterAwareInterface;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareInterface;
use Dvsa\Olcs\Api\Service\OpenAm\UserInterface;
use Dvsa\Olcs\Transfer\Command\Audit as AuditCommand;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;

/**
 * Abstract Query Handler
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractQueryHandler implements QueryHandlerInterface, FactoryInterface, AuthAwareInterface
{
    use AuthAwareTrait;

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
       return $this($serviceLocator, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {

        if ($this instanceof AuthAwareInterface) {
            $this->setAuthService($container->get(AuthorizationService::class));
        }

        if ($this instanceof UploaderAwareInterface) {
            $this->setUploader($container->get('FileUploader'));
        }

        if ($this instanceof \Dvsa\Olcs\Api\Domain\CpmsAwareInterface) {
            $this->setCpmsService($container->get('CpmsHelperService'));
        }

        if ($this instanceof \Dvsa\Olcs\Api\Domain\CompaniesHouseAwareInterface) {
            $this->setCompaniesHouseService($container->get('CompaniesHouseService'));
        }

        if ($this instanceof \Dvsa\Olcs\Address\Service\AddressServiceAwareInterface) {
            $this->setAddressService($container->get('AddressService'));
        }

        if ($this instanceof NationalRegisterAwareInterface) {
            $this->setNationalRegisterConfig($container->get('Config')['nr']);
        }

        if ($this instanceof OpenAmUserAwareInterface) {
            $this->setOpenAmUser($container->get(UserInterface::class));
        }

        $this->repoManager = $container->get('RepositoryServiceManager');

        $this->extraRepos[] = $this->repoServiceName;

        $this->queryHandler = $container;

        $this->commandHandler = $container->get('CommandHandlerManager');

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

        $entityToDtoMap = [
            \Dvsa\Olcs\Api\Entity\Organisation\Organisation::class => AuditCommand\ReadOrganisation::class,
            \Dvsa\Olcs\Api\Entity\Licence\Licence::class => AuditCommand\ReadLicence::class,
            \Dvsa\Olcs\Api\Entity\Cases\Cases::class => AuditCommand\ReadCase::class,
            \Dvsa\Olcs\Api\Entity\Application\Application::class => AuditCommand\ReadApplication::class,
            \Dvsa\Olcs\Api\Entity\Bus\BusReg::class => AuditCommand\ReadBusReg::class,
            \Dvsa\Olcs\Api\Entity\Tm\TransportManager::class => AuditCommand\ReadTransportManager::class,
        ];

        if (!isset($entityToDtoMap[get_class($entity)])) {
            throw new \RuntimeException('Cannot create audit read for entity, no DTO is defined');
        }

        $dto = $entityToDtoMap[get_class($entity)]::create(['id' => $entity->getId()]);

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
}
