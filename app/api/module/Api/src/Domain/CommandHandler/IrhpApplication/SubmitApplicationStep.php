<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactioningCommandHandler;
use Dvsa\Olcs\Api\Domain\FormControlServiceManager;
use Dvsa\Olcs\Api\Service\Qa\QaContextGenerator;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\SubmitApplicationStep as SubmitApplicationStepCmd;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Submit application step
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class SubmitApplicationStep extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'IrhpApplication';

    protected $extraRepos = ['IrhpPermitApplication'];

    /** @var QaContextGenerator */
    private $qaContextGenerator;

    /** @var FormControlServiceManager */
    private $formControlServiceManager;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator Service Manager
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        return $this->__invoke($serviceLocator, SubmitApplicationStep::class);
    }

    /**
     * Handle command
     *
     * @param SubmitApplicationStepCmd|CommandInterface $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $qaContext = $this->qaContextGenerator->generate(
            $command->getId(),
            $command->getIrhpPermitApplication(),
            $command->getSlug()
        );

        $formControlStrategy = $this->formControlServiceManager->getByApplicationStep(
            $qaContext->getApplicationStepEntity()
        );

        $destinationName = $formControlStrategy->saveFormData($qaContext, $command->getPostData());
        $this->result->addMessage($destinationName);

        $qaEntity = $qaContext->getQaEntity();
        $repositoryName = $qaEntity->getRepositoryName();
        $repository = $this->getRepo($repositoryName);

        if ($repository->contains($qaEntity)) {
            $qaEntity->onSubmitApplicationStep();
            $repository->save($qaEntity);
        }

        return $this->result;
    }
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $fullContainer = $container;
        
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $container = $container->getServiceLocator();
        }

        $this->qaContextGenerator = $container->get('QaContextGenerator');
        $this->formControlServiceManager = $container->get('FormControlServiceManager');
        return parent::__invoke($fullContainer, $requestedName, $options);
    }
}
