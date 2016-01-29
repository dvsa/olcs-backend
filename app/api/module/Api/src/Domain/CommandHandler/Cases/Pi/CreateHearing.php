<?php

/**
 * Create pi hearing
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Pi;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Pi\PresidingTc as PresidingTcEntity;
use Dvsa\Olcs\Api\Entity\Pi\Pi as PiEntity;
use Dvsa\Olcs\Api\Entity\Pi\PiHearing as PiHearingEntity;
use Dvsa\Olcs\Api\Entity\Pi\PiVenue as PiVenueEntity;
use Dvsa\Olcs\Transfer\Command\Cases\Pi\CreateHearing as CreateHearingCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Command\Publication\PiHearing as PublishHearingCmd;
use Dvsa\Olcs\Api\Entity\Task\Task as TaskEntity;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask as CreateTaskCmd;
use Doctrine\ORM\Query;

/**
 * Create pi hearing
 */
final class CreateHearing extends AbstractCommandHandler implements AuthAwareInterface, TransactionedInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'PiHearing';

    /**
     * Create pi hearing
     *
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var CreateHearingCmd $command */
        $result = new Result();

        /** @var PresidingTcEntity $presidingTc */
        $presidingTc = $this->getRepo()->getReference(PresidingTcEntity::class, $command->getPresidingTc());

        /** @var PiEntity $pi */
        $pi = $this->getRepo()->getReference(PiEntity::class, $command->getPi());

        /** @var RefData $presidingTcRole */
        $presidedByRole = $this->getRepo()->getRefdataReference($command->getPresidedByRole());

        $hearingDate = \DateTime::createFromFormat('Y-m-d H:i:s', $command->getHearingDate());

        if ($command->getPiVenue() === null) {
            $piVenue = null;
        } else {
            $piVenue = $this->getRepo()->getReference(PiVenueEntity::class, $command->getPiVenue());
        }

        $isAdjourned = $command->getIsAdjourned();
        $isCancelled = $command->getIsCancelled();

        $piHearing = new PiHearingEntity(
            $pi,
            $presidingTc,
            $presidedByRole,
            $hearingDate,
            $piVenue,
            $command->getPiVenueOther(),
            $command->getWitnesses(),
            $isCancelled,
            $command->getCancelledDate(),
            $command->getCancelledReason(),
            $isAdjourned,
            $command->getAdjournedDate(),
            $command->getAdjournedReason(),
            $command->getDetails()
        );

        $this->getRepo()->save($piHearing);
        $id = $piHearing->getId();
        $result->addMessage('Pi Hearing created');
        $result->addId('PiHearing', $id);

        if (($isAdjourned === 'N' || $isCancelled === 'N') && $command->getPublish() === 'Y') {
            $result->merge($this->getCommandHandler()->handleCommand($this->createPublishCommand($id, $command)));
        }

        if ($isAdjourned === 'Y') {
            $result->merge($this->getCommandHandler()->handleCommand($this->createTaskCommand($piHearing)));
        }

        return $result;
    }

    /**
     * @param PiHearingEntity $hearing
     * @return CreateTaskCmd
     */
    private function createTaskCommand(PiHearingEntity $hearing)
    {
        $currentUser = $this->getCurrentUser();

        $actionDate = date('Y-m-d', mktime(date("H"), date("i"), date("s"), date("n"), date("j")+7, date("Y")));

        $pi = $hearing->getPi();
        $case = $pi->getCase();

        $data = [
            'category' => TaskEntity::CATEGORY_COMPLIANCE,
            'subCategory' => TaskEntity::SUB_CATEGORY_HEARINGS_APPEALS,
            'description' => 'Verify adjournment of case',
            'actionDate' => $actionDate,
            'urgent' => 'Y',
            'assignedToUser' => $currentUser->getId(),
            'assignedToTeam' => $currentUser->getTeam()->getId(),
            'case' => $case->getId(),
        ];

        if ($case->isTm()) {
            $data['transportManager'] = $case->getTransportManager()->getId();
        } else {
            $data['licence'] = $case->getLicence()->getId();
        }

        return CreateTaskCmd::create($data);
    }

    /**
     * @param int $id
     * @param CreateHearingCmd $command
     * @return PublishHearingCmd
     */
    private function createPublishCommand($id, $command)
    {
        return PublishHearingCmd::create(
            [
                'id' => $id,
                'pubType' => [$command->getPubType()],
                'trafficAreas' => $command->getTrafficAreas(),
                'text2' => $command->getDetails()
            ]
        );
    }
}
