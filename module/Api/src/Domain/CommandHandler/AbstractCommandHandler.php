<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler;

use Dvsa\Olcs\Address\Service\AddressServiceAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\RedisAwareInterface;
use Dvsa\Olcs\Api\Domain\ConfigAwareInterface;
use Dvsa\Olcs\Api\Domain\DocumentGeneratorAwareInterface;
use Dvsa\Olcs\Api\Domain\Exception\DisabledHandlerException;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Domain\HandlerEnabledTrait;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareInterface;
use Dvsa\Olcs\Api\Domain\PublicationGeneratorAwareInterface;
use Dvsa\Olcs\Api\Domain\Repository\RepositoryInterface;
use Dvsa\Olcs\Api\Domain\SubmissionGeneratorAwareInterface;
use Dvsa\Olcs\Api\Domain\ToggleAwareInterface;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Domain\TransExchangeAwareInterface;
use Dvsa\Olcs\Api\Domain\UploaderAwareInterface;
use Dvsa\Olcs\Api\Service\Document\NamingServiceAwareInterface;
use Dvsa\Olcs\Api\Service\Ebsr\TransExchangeClient;
use Dvsa\Olcs\Api\Service\OpenAm\UserInterface;
use Dvsa\Olcs\Api\Service\Publication\PublicationGenerator;
use Dvsa\Olcs\Api\Service\Submission\SubmissionGenerator;
use Dvsa\Olcs\Api\Domain\FileProcessorAwareInterface;
use Dvsa\Olcs\Api\Service\Ebsr\FileProcessorInterface;
use Dvsa\Olcs\Api\Service\Toggle\ToggleService;
use Dvsa\Olcs\Queue\Factories\MessageBuilderFactory;
use Dvsa\Olcs\Queue\Service\Message\MessageBuilder;
use Dvsa\Olcs\Queue\Service\Queue;
use Dvsa\Olcs\Queue\Service\QueueInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Olcs\Logging\Log\Logger;
use Zend\Cache\Storage\Adapter\Redis;
use Zend\ServiceManager\Exception\ExceptionInterface as ZendServiceException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Service\AuthorizationService;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Rbac\PidIdentityProvider;
use Dvsa\Olcs\Api\Entity\System\RefData as RefDataEntity;

/**
 * Abstract Command Handler
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractCommandHandler implements CommandHandlerInterface, FactoryInterface
{
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
     * @var CommandHandlerInterface
     */
    private $commandHandler;

    /**
     * @var \Dvsa\Olcs\Api\Domain\QueryHandler\QueryHandlerInterface
     */
    private $queryHandler;

    private $repoManager;

    /**
     * @var PidIdentityProvider
     */
    private $pidIdentityProvider;

    /**
     * @var Result
     */
    protected $result;

    /**
     * @var array
     */
    protected $toggleConfig = [];

    /**
     * create service
     *
     * @param ServiceLocatorInterface $serviceLocator service locator
     *
     * @return $this|TransactioningCommandHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->result = new Result();

        /** @var ServiceLocatorInterface $mainServiceLocator */
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        try {
            $this->applyInterfaces($mainServiceLocator);
        } catch (ZendServiceException $e) {
            $this->logServiceExceptions($e);
        }

        $this->repoManager = $mainServiceLocator->get('RepositoryServiceManager');

        if ($this->repoServiceName !== null) {
            $this->extraRepos[] = $this->repoServiceName;
        }

        $this->commandHandler = $serviceLocator;

        $this->queryHandler = $mainServiceLocator->get('QueryHandlerManager');

        $this->pidIdentityProvider = $mainServiceLocator->get(PidIdentityProvider::class);

        if ($this instanceof TransactionedInterface) {
            return new TransactioningCommandHandler($this, $mainServiceLocator->get('TransactionManager'));
        }

        return $this;
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
            $this->setUserRepository($mainServiceLocator->get('RepositoryServiceManager')->get('User'));
        }

        if ($this instanceof DocumentGeneratorAwareInterface) {
            $this->setDocumentGenerator($mainServiceLocator->get('DocumentGenerator'));
        }

        if ($this instanceof PublicationGeneratorAwareInterface) {
            $this->setPublicationGenerator($mainServiceLocator->get(PublicationGenerator::class));
        }

        if ($this instanceof SubmissionGeneratorAwareInterface) {
            $this->setSubmissionGenerator($mainServiceLocator->get(SubmissionGenerator::class));
            $this->setSubmissionConfig($mainServiceLocator->get('Config')['submissions']['sections']['configuration']);
        }

        if ($this instanceof AddressServiceAwareInterface) {
            $this->setAddressService($mainServiceLocator->get('AddressService'));
        }

        if ($this instanceof \Dvsa\Olcs\Api\Domain\EmailAwareInterface) {
            $this->setTemplateRendererService(
                $mainServiceLocator->get(\Dvsa\Olcs\Email\Service\TemplateRenderer::class)
            );
        }

        if ($this instanceof \Dvsa\Olcs\Api\Domain\CompaniesHouseAwareInterface) {
            $companiesHouseService = $mainServiceLocator->get('CompaniesHouseService');
            $this->setCompaniesHouseService($companiesHouseService);
        }

        if ($this instanceof \Dvsa\Olcs\Api\Domain\CpmsAwareInterface) {
            $this->setCpmsService($mainServiceLocator->get('CpmsHelperService'));
        }

        if ($this instanceof \Dvsa\Olcs\Api\Domain\TranslatorAwareInterface) {
            $translator = $mainServiceLocator->get('translator');
            $this->setTranslator($translator);
        }

        if ($this instanceof UploaderAwareInterface) {
            $this->setUploader($mainServiceLocator->get('FileUploader'));
        }

        if ($this instanceof NamingServiceAwareInterface) {
            $this->setNamingService($mainServiceLocator->get('DocumentNamingService'));
        }

        if ($this instanceof OpenAmUserAwareInterface) {
            $this->setOpenAmUser($mainServiceLocator->get(UserInterface::class));
        }

        if ($this instanceof TransExchangeAwareInterface) {
            $this->setTransExchange($mainServiceLocator->get(TransExchangeClient::class));
        }

        if ($this instanceof ConfigAwareInterface) {
            $this->setConfig($mainServiceLocator->get('Config'));
        }

        if ($this instanceof FileProcessorAwareInterface) {
            $this->setFileProcessor($mainServiceLocator->get(FileProcessorInterface::class));
        }


        if ($this instanceof QueueInterface) {
            $this->setQueueService($mainServiceLocator->get(Queue::class));
            $this->setMessageBuilderService($mainServiceLocator->get(MessageBuilder::class));
            $this->setQueueConfig($mainServiceLocator->get('Config')['message_queue']);
        }

        if ($this instanceof RedisAwareInterface) {
            /** @var Redis $redis */
            $redis = $mainServiceLocator->get(Redis::class);
            $this->setRedis($redis);

        }
    }

    /**
     * get repository
     *
     * @param string|null $name name of repository
     *
     * @return RepositoryInterface
     * @throws RuntimeException
     */
    protected
    function getRepo(
        $name = null
    ) {
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
     * get command handler
     *
     * @return \Dvsa\Olcs\Api\Domain\CommandHandlerManager
     */
    protected
    function getCommandHandler()
    {
        return $this->commandHandler;
    }

    /**
     * get query handler
     *
     * @return \Dvsa\Olcs\Api\Domain\QueryHandlerManager
     */
    protected
    function getQueryHandler()
    {
        return $this->queryHandler;
    }

    /**
     * Get PidIdentityProvider
     *
     * @return PidIdentityProvider
     */
    protected
    function getPidIdentityProvider()
    {
        return $this->pidIdentityProvider;
    }

    /**
     * Wrapper to call a query
     *
     * @param QueryInterface $query query
     *
     * @return \Dvsa\Olcs\Api\Domain\Query\Result
     */
    protected
    function handleQuery(
        QueryInterface $query
    ) {
        try {
            $result = $this->getQueryHandler()->handleQuery($query, false);
        } catch (DisabledHandlerException $e) {
            $result = new Result();
            $result->addMessage($e->getMessage());
        }

        return $result;
    }

    /**
     * Wrapper to call another command
     *
     * @param CommandInterface $command command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    protected
    function handleSideEffect(
        CommandInterface $command
    ) {
        try {
            $result = $this->getCommandHandler()->handleCommand($command, false);
        } catch (DisabledHandlerException $e) {
            $result = new Result();
            $result->addMessage($e->getMessage());
        }

        return $result;
    }

    /**
     * Wrapper to call an array of other commands
     *
     * @param array $commands array of commands
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    protected
    function handleSideEffects(
        array $commands
    ) {
        $result = new Result();

        foreach ($commands as $command) {
            $result->merge($this->handleSideEffect($command));
        }

        return $result;
    }

    /**
     * Wrapper to call another command as a system user
     *
     * @param CommandInterface $command command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    protected
    function handleSideEffectAsSystemUser(
        CommandInterface $command
    ) {
        $pidIdentityProvider = $this->getPidIdentityProvider();
        $pidIdentityProvider->setMasqueradedAsSystemUser(true);
        $result = $this->getCommandHandler()->handleCommand($command, false);
        $pidIdentityProvider->setMasqueradedAsSystemUser(false);

        return $result;
    }

    /**
     * Wrapper to call an array of other commands as a system user
     *
     * @param array $commands commands
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    protected
    function handleSideEffectsAsSystemUser(
        array $commands
    ) {
        $pidIdentityProvider = $this->getPidIdentityProvider();
        $pidIdentityProvider->setMasqueradedAsSystemUser(true);
        $result = $this->handleSideEffects($commands);
        $pidIdentityProvider->setMasqueradedAsSystemUser(false);

        return $result;
    }

    /**
     * Proxy to another command, using all data from the original command
     *
     * @param CommandInterface $originalCommand       original command
     * @param string           $proxyCommandClassName proxy class name
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    protected
    function proxyCommand(
        $originalCommand,
        $proxyCommandClassName
    ) {
        $dtoData = $originalCommand->getArrayCopy();

        return $this->handleSideEffect($proxyCommandClassName::create($dtoData));
    }

    /**
     * Proxy to another command as a system user, using all data from the original command
     *
     * @param CommandInterface $originalCommand       original command
     * @param string           $proxyCommandClassName proxy command class name
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    protected
    function proxyCommandAsSystemUser(
        $originalCommand,
        $proxyCommandClassName
    ) {
        $pidIdentityProvider = $this->getPidIdentityProvider();
        $pidIdentityProvider->setMasqueradedAsSystemUser(true);
        $result = $this->proxyCommand($originalCommand, $proxyCommandClassName);
        $pidIdentityProvider->setMasqueradedAsSystemUser(true);

        return $result;
    }

    /**
     * Returns collection of entityClass objects.
     *
     * @param string $entityClass  class name
     * @param array  $referenceIds reference ids
     *
     * @return ArrayCollection
     */
    protected
    function buildArrayCollection(
        $entityClass = '',
        $referenceIds = []
    ) {
        $collection = new ArrayCollection();

        if (!empty($referenceIds)) {
            foreach ($referenceIds as $referenceId) {
                $collection->add($this->getRepo()->getReference($entityClass, $referenceId));
            }
        }

        return $collection;
    }

    /**
     * Generates a variable from a given command, or if not present in the command, returns a default value.
     *
     * @param CommandInterface $command      command
     * @param string           $variableName variable name
     * @param mixed            $defaultValue default value
     *
     * @return mixed
     */
    protected
    function extractCommandVariable(
        $command,
        $variableName,
        $defaultValue = null
    ) {
        $commandVariable = $defaultValue;

        $methodName = 'get' . ucfirst($variableName);
        if (method_exists($command, $methodName)) {
            $commandVariable = $command->$methodName();
        }

        return $commandVariable;
    }

    /**
     * Returns a ref data reference for the provided key, or null if the key is null or doesn't exist
     *
     * @param string|null $refDataKey ref data key
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData|null
     */
    protected
    function refDataOrNull(
        $refDataKey
    ) {
        if ($refDataKey === null) {
            return null;
        }

        return $this->refData($refDataKey);
    }

    /**
     * For required fields we can skip the null refdata check
     *
     * @param string $refDataKey
     *
     * @return RefDataEntity
     */
    protected
    function refData(
        string $refDataKey
    ): RefDataEntity {
        return $this->getRepo()->getRefdataReference($refDataKey);
    }

    /**
     * @param string|null $value ('Y' or 'N')
     *
     * @return bool|null
     */
    protected
    function yesNoToBoolOrNull(
        ?string $value
    ): ?bool {
        if ($value === null || ($value !== 'Y' && $value !== 'N')) {
            return null;
        }
        return $value === 'Y' ? true : false;
    }
}
