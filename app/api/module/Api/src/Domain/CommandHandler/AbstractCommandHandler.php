<?php

/**
 * Abstract Command Handler
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler;

use Dvsa\Olcs\Address\Service\AddressServiceAwareInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\DocumentGeneratorAwareInterface;
use Dvsa\Olcs\Api\Domain\PublicationGeneratorAwareInterface;
use Dvsa\Olcs\Api\Domain\SubmissionGeneratorAwareInterface;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\Repository\RepositoryInterface;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Service\Publication\PublicationGenerator;
use Dvsa\Olcs\Api\Service\Submission\SubmissionGenerator;
use Dvsa\Olcs\Api\Service\Submission\SubmissionCommentService;

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

        $this->applyInterfaces($mainServiceLocator);

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
            $this->setEmailService($mainServiceLocator->get(\Dvsa\Olcs\Email\Service\Client::class));
            $this->setTemplateRendererService(
                $mainServiceLocator->get(\Dvsa\Olcs\Email\Service\TemplateRenderer::class)
            );
        }

        if ($this instanceof \Dvsa\Olcs\Api\Domain\CompaniesHouseAwareInterface) {
            $companiesHouseService = $mainServiceLocator->get('serviceFactory')->getService('CompaniesHouse');
            $this->setCompaniesHouseService($companiesHouseService);
        }

        if ($this instanceof \Dvsa\Olcs\Api\Domain\CpmsAwareInterface) {
            // // check config to see which api helper to use
            // $config = $mainServiceLocator->get('Config');
            // if (isset($config['cpms_api']['version']) && $config['cpms_api']['version'] == '2') {
            //     $this->setCpmsService($mainServiceLocator->get('CpmsV2HelperService'));
            // }
            // else {
                $this->setCpmsService($mainServiceLocator->get('CpmsHelperService'));
            // }
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
