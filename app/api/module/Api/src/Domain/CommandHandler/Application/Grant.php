<?php

/**
 * Grant
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Transfer\Command\Application\Grant as Cmd;
use Dvsa\Olcs\Transfer\Command\InspectionRequest\CreateFromGrant;
use Dvsa\Olcs\Api\Domain\Command\Application\GrantGoods as GrantGoodsCmd;
use Dvsa\Olcs\Api\Domain\Command\Application\GrantPsv as GrantPsvCmd;

/**
 * Grant
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class Grant extends AbstractCommandHandler implements TransactionedInterface
{
    const ERROR_IR_DUE_DATE = 'APP-GRA-IR-DD-1';

    protected $repoServiceName = 'Application';

    /**
     * @var \Dvsa\Olcs\Api\Service\Lva\Application\GrantValidationService
     */
    private $grantValidationService;

    /**
     * Create service
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator service locator
     *
     * @return $this|\Dvsa\Olcs\Api\Domain\CommandHandler\TransactioningCommandHandler|mixed
     */
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->grantValidationService = $mainServiceLocator->get('ApplicationGrantValidationService');

        return parent::createService($serviceLocator);
    }

    /**
     * Handle command
     *
     * @param Cmd $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        if ($command->getShouldCreateInspectionRequest() === 'Y'
            && $command->getDueDate() === null
        ) {
            throw new ValidationException(
                [
                    'dueDate' => [
                        [self::ERROR_IR_DUE_DATE => 'Due date is required']
                    ]
                ]
            );
        }

        $result = new Result();

        /** @var ApplicationEntity $application */
        $application = $this->getRepo()->fetchUsingId($command);

        $errors = $this->grantValidationService->validate($application);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        if ($application->isGoods()) {
            $result->merge($this->proxyCommand($command, GrantGoodsCmd::class));
        } else {
            $result->merge($this->proxyCommand($command, GrantPsvCmd::class));
        }

        $application->setGrantAuthority($this->refData($command->getGrantAuthority()));

        if ($command->getShouldCreateInspectionRequest() == 'Y') {
            if ($application->isGoods()) {
                $result->merge(
                    $this->saveInspectionRequestDetails(
                        $application,
                        $command->getDueDate(),
                        $command->getNotes()
                    )
                );
            } else {
                $result->merge(
                    $this->createInspectionRequest(
                        $application->getId(),
                        $command->getDueDate(),
                        $command->getNotes()
                    )
                );
            }
        } elseif ($application->isGoods()) {
            $application->setRequestInspection(false);
            $this->getRepo()->save($application);
        }

        if ($application->isNew()) {
            $result->merge($this->publishApplication($application));
            $result->merge($this->closeTexTask($application));
        }

        return $result;
    }

    /**
     * Create inspection request
     *
     * @param int    $applicationId   application id
     * @param int    $duePeriod       due period
     * @param string $caseworkerNotes caseworker notes
     *
     * @return Result
     */
    private function createInspectionRequest($applicationId, $duePeriod, $caseworkerNotes)
    {
        $data = [
            'application' => $applicationId,
            'duePeriod' => $duePeriod,
            'caseworkerNotes' => $caseworkerNotes
        ];
        return $this->handleSideEffectAsSystemUser(CreateFromGrant::create($data));
    }

    /**
     * Save inspection request details
     *
     * @param Application $application     application
     * @param int         $duePeriod       due period
     * @param string      $caseworkerNotes caseworker notes
     *
     * @return Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function saveInspectionRequestDetails($application, $duePeriod, $caseworkerNotes)
    {
        $application->setRequestInspection(true);
        $application->setRequestInspectionDelay($duePeriod);
        $application->setRequestInspectionComment($caseworkerNotes);
        $this->getPidIdentityProvider()->setMasqueradedAsSystemUser(true);
        $this->getRepo()->save($application);
        $this->getPidIdentityProvider()->setMasqueradedAsSystemUser(false);
        $result = new Result();
        $result->addMessage('Inspection request details saved');
        return $result;
    }

    /**
     * Publish the application
     *
     * @param ApplicationEntity $application application
     *
     * @return Result
     */
    protected function publishApplication(ApplicationEntity $application)
    {
        return $this->handleSideEffectAsSystemUser(
            \Dvsa\Olcs\Transfer\Command\Publication\Application::create(
                [
                    'id' => $application->getId(),
                    'trafficArea' => $application->getTrafficArea()->getId(),
                    'publicationSection' => \Dvsa\Olcs\Api\Entity\Publication\PublicationSection::APP_GRANTED_SECTION,
                ]
            )
        );
    }

    /**
     * Close any TEX tasks on the application
     *
     * @param ApplicationEntity $application application
     *
     * @return Result
     */
    protected function closeTexTask(ApplicationEntity $application)
    {
        return $this->handleSideEffectAsSystemUser(
            \Dvsa\Olcs\Api\Domain\Command\Application\CloseTexTask::create(
                [
                    'id' => $application->getId(),
                ]
            )
        );
    }
}
