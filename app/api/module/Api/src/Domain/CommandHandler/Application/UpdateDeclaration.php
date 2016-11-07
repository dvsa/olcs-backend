<?php

/**
 * UpdateDeclaration
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Domain\Command\Application\CancelAllInterimFees as CancelAllInterimFeesCommand;
use Dvsa\Olcs\Api\Domain\Command\Application\CreateFee as CreateFeeCommand;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Transfer\Command\Application\UpdateDeclaration as UpdateDeclarationCommand;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion as UpdateApplicationCompletionCommand;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;

/**
 * UpdateDeclaration
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class UpdateDeclaration extends AbstractCommandHandler implements TransactionedInterface
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

        $this->getRepo()->save($application);

        // if interimRequested is Y or N (eg it is specified)
        if ($command->getInterimRequested() === 'Y' || $command->getInterimRequested() === 'N') {
            $interimFeeResult = $this->handleInterimFee($command, $application->isVariation());
            if ($interimFeeResult instanceof Result) {
                $result->merge($interimFeeResult);
            }
        }

        // update completion
        $result->merge($this->updateApplicationCompletionCommand($command));

        $result->addId('application', $application->getId());
        $result->addMessage('Update declaration successful');
        return $result;
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
        return $this->handleSideEffect(
            UpdateApplicationCompletionCommand::create(
                ['id' => $command->getId(), 'section' => 'undertakings']
            )
        );
    }

    /**
     * Handle what should happen with the interim fee
     *
     * @param UpdateDeclarationCommand $command     update declaration command
     * @param bool                     $isVariation is variation
     *
     * @return Result|false
     */
    private function handleInterimFee(UpdateDeclarationCommand $command, $isVariation)
    {
        // if interim is requested
        if ($command->getInterimRequested() === 'Y') {
            if ($this->shouldCreateInterimFee($command, $isVariation)) {
                return $this->handleSideEffect(
                    CreateFeeCommand::create(
                        [
                            'id' => $command->getId(),
                            'feeTypeFeeType' => FeeTypeEntity::FEE_TYPE_GRANTINT
                        ]
                    )
                );
            }
        } else {
            return $this->handleSideEffect(
                CancelAllInterimFeesCommand::create(['id' => $command->getId()])
            );
        }

        return false;
    }

    /**
     * Should we create an interim fee?
     *
     * @param UpdateDeclarationCommand $command     update declaration command
     * @param bool                     $isVariation is variation
     *
     * @return bool
     */
    private function shouldCreateInterimFee(UpdateDeclarationCommand $command, $isVariation)
    {
        $id = $command->getId();

        $outstandingInterimFees = $this->feeRepo->fetchInterimFeesByApplicationId($id, true);
        if (!$isVariation && empty($outstandingInterimFees)) {
            return true;
        }

        $variationFees = $this->feeRepo->fetchFeeByTypeAndApplicationId(
            FeeTypeEntity::FEE_TYPE_VAR,
            $id
        );

        if ($isVariation && empty($outstandingInterimFees) && !empty($variationFees)) {
            return true;
        }

        return false;
    }
}
