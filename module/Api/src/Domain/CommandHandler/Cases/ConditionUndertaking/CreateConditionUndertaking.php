<?php

/**
 * Create ConditionUndertaking
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cases\ConditionUndertaking;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Cases\Cases;
use Dvsa\Olcs\Api\Entity\Cases\ConditionUndertaking;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre;
use Dvsa\Olcs\Transfer\Command\Cases\ConditionUndertaking\CreateConditionUndertaking as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;

/**
 * Create ConditionUndertaking
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
final class CreateConditionUndertaking extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'ConditionUndertaking';

    protected $extraRepos = ['Cases', 'Licence', 'OperatingCentre'];

    /**
     * Creates ConditionUndertaking
     *
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $conditionUndertaking = $this->createConditionUndertakingObject($command);

        $this->getRepo()->save($conditionUndertaking);
        $result->addMessage('ConditionUndertaking created');

        return $result;
    }

    /**
     * Create the ConditionUndertaking object
     *
     * @param Cmd $command
     * @return ConditionUndertaking
     */
    private function createConditionUndertakingObject(Cmd $command)
    {
        $isDraft = 'N';
        $case = $this->getRepo('Cases')->fetchById($command->getCase());
        $licence = $this->getRepo('Licence')->fetchById($case->getLicence()->getId());

        $conditionUndertaking = new ConditionUndertaking(
            $this->getRepo()->getRefdataReference($command->getConditionType()),
            $command->getIsFulfilled(),
            $isDraft
        );
        $conditionUndertaking->setCase($case);
        $conditionUndertaking->setAttachedTo($this->getRepo()->getRefdataReference($command->getAttachedTo()));
        $conditionUndertaking->setLicence($licence);

        $conditionUndertaking->setAddedVia($this->getRepo()->getRefdataReference(ConditionUndertaking::ADDED_VIA_CASE));

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
