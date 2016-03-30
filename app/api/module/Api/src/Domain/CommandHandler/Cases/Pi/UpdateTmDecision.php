<?php

/**
 * Update pi decision
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Pi;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Pi\PresidingTc as PresidingTcEntity;
use Dvsa\Olcs\Api\Entity\Pi\Pi as PiEntity;
use Dvsa\Olcs\Api\Entity\Pi\Decision as PiDecisionEntity;
use Dvsa\Olcs\Transfer\Command\Cases\Pi\UpdateDecision as UpdateDecisionCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Command\Publication\PiDecision as PublishDecisionCmd;
use Doctrine\ORM\Query;

/**
 * Update pi decision
 */
final class UpdateTmDecision extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Pi';

    protected $extraRepos = ['PresidingTc'];

    /**
     * Update pi decision
     *
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var UpdateDecisionCmd $command */
        $result = new Result();

        $decisions = $this->buildArrayCollection(PiDecisionEntity::class, $command->getDecisions());

        // build an empty ArrayCollection
        $tmDecisions = $this->buildArrayCollection();

        /** @var PiEntity $pi */
        $pi = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        /** @var PresidingTcEntity $presidingTc */
        $presidingTc = $this->getRepo()->getReference(PresidingTcEntity::class, $command->getDecidedByTc());

        /** @var RefData $presidingTcRole */
        $presidingTcRole = $this->getRepo()->getRefdataReference($command->getDecidedByTcRole());

        $pi->updatePiWithDecision(
            $presidingTc,
            $presidingTcRole,
            $decisions,
            null,
            null,
            null,
            $command->getWitnesses(),
            $command->getDecisionDate(),
            $command->getNotificationDate(),
            $command->getDecisionNotes(),
            null,
            $tmDecisions
        );

        $this->getRepo()->save($pi);
        $result->addMessage('Decision created');
        $result->addId('Pi', $pi->getId());

        if ($command->getPublish() === 'Y') {
            $result->merge($this->getCommandHandler()->handleCommand($this->createPublishCommand($pi, $command)));
        }

        return $result;
    }

    /**
     * @param $pi
     * @param $command
     * @return static
     * @throws ValidationException
     */
    private function createPublishCommand($pi, $command)
    {
        if (empty($pi->getPiHearings())) {
            throw new ValidationException(['This Public Inquiry does not have any hearings to publish']);
        }

        /**
         * @var PiEntity $pi
         * @var UpdateDecisionCmd $command
         */
        return PublishDecisionCmd::create(
            [
                'id' => $pi->getPiHearings()->last()->getId(),
                'pubType' => [$command->getPubType()],
                'trafficAreas' => $command->getTrafficAreas(),
                'text2' => $command->getDecisionNotes()
            ]
        );
    }
}
