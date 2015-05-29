<?php

/**
 * Update Type Of Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Transfer\Command\Application\UpdateTypeOfLicence as Cmd;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Domain\Command\Application\ResetApplication as ResetApplicationCommand;
use Dvsa\Olcs\Api\Domain\Command\Application\CreateApplicationFee as CreateApplicationFeeCommand;
use Dvsa\Olcs\Api\Domain\Command\Application\GenerateLicenceNumber as GenerateLicenceNumberCommand;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion as UpdateApplicationCompletionCommand;
use Dvsa\Olcs\Api\Domain\Command\Licence\CancelLicenceFees;

/**
 * Update Type Of Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateTypeOfLicence extends AbstractCommandHandler
{
    protected $repoServiceName = 'Application';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var Application $application */
        $application = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        // Early return if we haven't changed anything
        if (!$this->hasChangedTypeOfLicence($application, $command)) {
            $result->addMessage('No updates required');
            return $result;
        }

        if ($this->changeRequiresConfirmation($application, $command)) {
            return $this->getCommandHandler()->handleCommand($this->createResetApplicationCommand($command));
        }

        $sideEffects = $this->determineSideEffects($application, $command);

        $application->updateTypeOfLicence(
            $command->getNiFlag(),
            $this->getRepo()->getRefdataReference($command->getOperatorType()),
            $this->getRepo()->getRefdataReference($command->getLicenceType())
        );

        try {
            $this->getRepo()->beginTransaction();

            $this->getRepo()->save($application);

            foreach ($sideEffects as $sideEffect) {
                $result->merge($this->getCommandHandler()->handleCommand($sideEffect));
            }

            $this->getRepo()->commit();

            $result->addMessage('Application saved successfully');
            return $result;
        } catch (\Exception $ex) {
            $this->getRepo()->rollback();
            throw $ex;
        }
    }

    private function determineSideEffects(Application $application, Cmd $command)
    {
        $sideEffects = [];

        if ($this->updatingForTheFirstTime($application)) {

            $sideEffects[] = $this->createCreateApplicationFeeCommand($application);
            $sideEffects[] = $this->createGenerateLicenceNumberCommand($application);

        } elseif ($this->licenceTypeWillChange($application, $command)) {

            $sideEffects[] = $this->createCancelLicenceFeesCommand($application->getLicence());
            $sideEffects[] = $this->createCreateApplicationFeeCommand($application);

        }

        $sideEffects[] = $this->createUpdateApplicationCompletionCommand($application);

        return $sideEffects;
    }

    /**
     * Check whether we have changed anything
     *
     * @param Application $application
     * @param Cmd $command
     * @return bool
     */
    private function hasChangedTypeOfLicence(Application $application, Cmd $command)
    {
        return $application->getNiFlag() !== $command->getNiFlag()
            || $application->getLicenceType() !== $this->getRepo()->getRefdataReference($command->getLicenceType())
            || $application->getGoodsOrPsv() !== $this->getRepo()->getRefdataReference($command->getOperatorType());
    }

    private function createCreateApplicationFeeCommand(Application $application)
    {
        return CreateApplicationFeeCommand::create(['id' => $application->getId()]);
    }

    private function createCancelLicenceFeesCommand(Licence $licence)
    {
        return CancelLicenceFees::create(['id' => $licence->getId()]);
    }

    private function createGenerateLicenceNumberCommand(Application $application)
    {
        return GenerateLicenceNumberCommand::create(['id' => $application->getId()]);
    }

    private function createUpdateApplicationCompletionCommand(Application $application)
    {
        return UpdateApplicationCompletionCommand::create(
            ['id' => $application->getId(), 'section' => 'typeOfLicence']
        );
    }

    private function createResetApplicationCommand(Cmd $command)
    {
        return ResetApplicationCommand::create($command->getArrayCopy());
    }

    /**
     * Whether the changes require confirmation
     *
     * @param Application $application
     * @param Cmd $command
     * @return boolean
     */
    private function changeRequiresConfirmation(Application $application, Cmd $command)
    {
        return !$this->updatingForTheFirstTime($application) && (
            $this->typeOfLicenceWillChange($application, $command)
            || $this->changingToOrFromSr($application, $command)
        );
    }

    /**
     * Whether we are changing to or from special restricted
     *
     * @param Application $application
     * @param Cmd $command
     * @return boolean
     */
    private function changingToOrFromSr(Application $application, Cmd $command)
    {
        $sr = $this->getRepo()->getRefdataReference(Licence::LICENCE_TYPE_SPECIAL_RESTRICTED);
        $newLicenceType = $this->getRepo()->getRefdataReference($command->getLicenceType());

        return $this->licenceTypeWillChange($application, $command)
            && ($application->getLicenceType() === $sr || $newLicenceType === $sr);
    }

    /**
     * Whether the current TOL values are null
     *
     * @param Application $application
     * @param Cmd $command
     * @return boolean
     */
    private function updatingForTheFirstTime(Application $application)
    {
        return $application->getNiFlag() === null
            || $application->getGoodsOrPsv() === null
            || $application->getLicenceType() === null;
    }

    /**
     * Whether we are changing niFlag or goodsOrPsv
     *
     * @param Application $application
     * @param Cmd $command
     * @return boolean
     */
    private function typeOfLicenceWillChange(Application $application, Cmd $command)
    {
        return $application->getNiFlag() !== $command->getNiFlag()
            || $application->getGoodsOrPsv() !== $this->getRepo()->getRefdataReference($command->getOperatorType());
    }

    /**
     * Whether we are changing licenceType
     *
     * @param Application $application
     * @param Cmd $command
     * @return boolean
     */
    private function licenceTypeWillChange(Application $application, Cmd $command)
    {
        return $application->getLicenceType() !== $this->getRepo()->getRefdataReference($command->getLicenceType());
    }
}
