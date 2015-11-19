<?php

/**
 * Abstract Command Handler
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler;

use Dvsa\Olcs\Address\Service\AddressServiceAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\DocumentGeneratorAwareInterface;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Domain\PublicationGeneratorAwareInterface;
use Dvsa\Olcs\Api\Domain\Repository\RepositoryInterface;
use Dvsa\Olcs\Api\Domain\SubmissionGeneratorAwareInterface;
use Dvsa\Olcs\Api\Domain\TransExchangeAwareInterface;
use Dvsa\Olcs\Api\Domain\UploaderAwareInterface;
use Dvsa\Olcs\Api\Service\Document\NamingServiceAwareInterface;
use Dvsa\Olcs\Api\Service\Ebsr\TransExchangeClient;
use Dvsa\Olcs\Api\Service\Publication\PublicationGenerator;
use Dvsa\Olcs\Api\Service\Submission\SubmissionGenerator;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Olcs\Logging\Log\Logger;
use Zend\ServiceManager\Exception\ExceptionInterface as ZendServiceException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Service\AuthorizationService;

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

    private $repoManager;

    /**
     * @var Result
     */
    protected $result;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->result = new Result();

        /** @var ServiceLocatorInterface $mainServiceLocator  */
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

        if ($this instanceof TransactionedInterface) {
            return new TransactioningCommandHandler($this, $mainServiceLocator->get('TransactionManager'));
        }

        return $this;
    }

    /**
     * Zend ServiceManager masks exceptions beind a simple 'service not created'
     * message so here we inspect the 'previous exception' chain and log out
     * what the actual errors were, before rethrowing the original execption.
     *
     * @param \Exception $e
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
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     *
     * Warnings suppressed as by design this is just a series of 'if' conditions
     */
    private function applyInterfaces($mainServiceLocator)
    {
        if ($this instanceof AuthAwareInterface) {
            $this->setAuthService($mainServiceLocator->get(AuthorizationService::class));
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

        if ($this instanceof TransExchangeAwareInterface) {
            $this->setTransExchange($mainServiceLocator->get(TransExchangeClient::class));
        }
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
     * Wrapper to call another command
     *
     * @param CommandInterface $command
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    protected function handleSideEffect(CommandInterface $command)
    {
        return $this->getCommandHandler()->handleCommand($command);
    }

    /**
     * Wrapper to call an array of other commands
     *
     * @param array CommandInterface $commands
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    protected function handleSideEffects(array $commands)
    {
        $result = new Result();

        foreach ($commands as $command) {
            $result->merge($this->handleSideEffect($command));
        }

        return $result;
    }

    /**
     * Proxy to another command, using all data from the original command
     *
     * @param $originalCommand
     * @param $proxyCommandClassName
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    protected function proxyCommand($originalCommand, $proxyCommandClassName)
    {
        $dtoData = $originalCommand->getArrayCopy();
        return $this->handleSideEffect($proxyCommandClassName::create($dtoData));
    }
}
