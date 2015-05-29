<?php

/**
 * UpdateDeclaration
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Domain\Command\Application\CancelAllInterimFees;
use Dvsa\Olcs\Api\Domain\Command\Application\CreateFee as CreateFeeCommand;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Transfer\Command\Application\UpdateDeclaration as UpdateDeclarationCommand;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion as UpdateApplicationCompletionCommand;

/**
 * UpdateDeclaration
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class UpdateDeclaration extends AbstractCommandHandler
{
    protected $repoServiceName = 'Application';

    /**
     * @var \Dvsa\Olcs\Api\Domain\Repository\Fee
     */
    protected $feeRepo;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->feeRepo = $serviceLocator->getServiceLocator()->get('RepositoryServiceManager')
            ->get('Fee');

        return parent::createService($serviceLocator);
    }

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /* @var $application Application  */
        $application = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());
        $application->setDeclarationConfirmation($command->getDeclarationConfirmation());
        if ($command->getInterimRequested() === 'Y') {
            $application->setInterimStatus(
                $this->getRepo()->getRefdataReference(Application::INTERIM_STATUS_REQUESTED)
            );
            $application->setInterimReason($command->getInterimReason());
        }
        if ($command->getInterimRequested() === 'N') {
            $application->setInterimStatus(null);
            $application->setInterimReason(null);
        }

        try {
            $this->getRepo()->beginTransaction();

            $this->getRepo()->save($application);

            // if interimRequested is Y or N (eg it is specified)
            if ($command->getInterimRequested() === 'Y' || $command->getInterimRequested() === 'N') {
                $interimFeeResult = $this->handleInterimFee($command);
                if ($interimFeeResult instanceof Result) {
                    $result->merge($interimFeeResult);
                }
            }

            // update completion
            $result->merge($this->updateApplicationCompletionCommand($command));

            $this->getRepo()->commit();

            $result->addId('application', $application->getId());
            $result->addMessage('Update declaration successful');
            return $result;
        } catch (\Exception $ex) {
            $this->getRepo()->rollback();
            throw $ex;
        }
    }

    /**
     * Update the application completion
     *
     * @param UpdateDeclarationCommand $command
     *
     * @return Result
     */
    private function updateApplicationCompletionCommand(UpdateDeclarationCommand $command)
    {
        return $this->getCommandHandler()->handleCommand(
            UpdateApplicationCompletionCommand::create(
                ['id' => $command->getId(), 'section' => 'undertakings']
            )
        );
    }

    /**
     * Handle what should happen with the interim fee
     *
     * @param UpdateDeclarationCommand $command
     *
     * @return Result|false
     */
    private function handleInterimFee(UpdateDeclarationCommand $command)
    {
        // if interim is requested
        if ($command->getInterimRequested() === 'Y') {
            // get existing interim fees
            $outstandingInterimFees = $this->feeRepo->fetchInterimFeesByApplicationId($command->getId(), true);
            // if there are no existing interim fees then create interim fee
            if (empty($outstandingInterimFees)) {
                return $this->getCommandHandler()->handleCommand(
                    CreateFeeCommand::create(
                        [
                            'id' => $command->getId(),
                            'feeTypeFeeType' => \Dvsa\Olcs\Api\Entity\Fee\FeeType::FEE_TYPE_GRANTINT
                        ]
                    )
                );
            }
        } else {
            return $this->getCommandHandler()->handleCommand(
                CancelAllInterimFees::create(['id' => $command->getId()])
            );
        }

        return false;
    }
}
