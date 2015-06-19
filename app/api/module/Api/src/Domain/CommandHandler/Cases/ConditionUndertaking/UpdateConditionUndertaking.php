<?php

/**
 * Update ConditionUndertaking
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cases\ConditionUndertaking;

use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Cases\ConditionUndertaking;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre;
use Dvsa\Olcs\Transfer\Command\Cases\ConditionUndertaking\UpdateConditionUndertaking as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;

/**
 * Update ConditionUndertaking
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
final class UpdateConditionUndertaking extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'ConditionUndertaking';

    /**
     * Update complaint
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $conditionUndertaking = $this->getRepo()->fetchUsingId(
            $command,
            Query::HYDRATE_OBJECT,
            $command->getVersion()
        );

        $conditionUndertaking = $this->updateConditionUndertakingObject($command, $conditionUndertaking);

        $this->getRepo()->save($conditionUndertaking);
        $result->addMessage('ConditionUndertaking updated');

        return $result;
    }

    /**
     * @param Cmd $command
     * @param ConditionUndertaking $conditionUndertaking
     * @return ConditionUndertaking
     */
    private function updateConditionUndertakingObject(Cmd $command, ConditionUndertaking $conditionUndertaking)
    {
        $conditionUndertaking->setConditionType($this->getRepo()->getRefdataReference($command->getConditionType()));
        $conditionUndertaking->setNotes($command->getNotes());

        if ($command->getIsFulfilled() !== null) {
            $conditionUndertaking->setIsFulfilled($command->getIsFulfilled());
        }

        $conditionUndertaking = $this->setAttachedToProperties($conditionUndertaking, $command);

        return $conditionUndertaking;
    }

    /**
     * Sets the AttachedTo and if required the Operating Centre
     *
     * @param ConditionUndertaking $conditionUndertaking
     * @param Cmd $command
     * @return ConditionUndertaking
     */
    private function setAttachedToProperties(ConditionUndertaking $conditionUndertaking, Cmd $command)
    {
        if ($command->getAttachedTo() == ConditionUndertaking::ATTACHED_TO_LICENCE) {
            $conditionUndertaking->setAttachedTo(
                $this->getRepo()->getRefdataReference(
                    ConditionUndertaking::ATTACHED_TO_LICENCE
                )
            );
            $conditionUndertaking->setOperatingCentre(null);

        } else {
            $operatingCentre = $this->getRepo()
                ->getReference(OperatingCentre::class, $command->getOperatingCentre());
            $conditionUndertaking->setOperatingCentre($operatingCentre);
            $conditionUndertaking->setAttachedTo(
                $this->getRepo()->getRefdataReference(
                    ConditionUndertaking::ATTACHED_TO_OPERATING_CENTRE
                )
            );
        }
        return $conditionUndertaking;
    }
}
