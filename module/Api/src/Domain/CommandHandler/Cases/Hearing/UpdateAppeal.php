<?php

/**
 * Update Appeal
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Hearing;

use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Cases\Appeal;
use Dvsa\Olcs\Transfer\Command\Cases\Hearing\UpdateAppeal as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;

/**
 * Update Appeal
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
final class UpdateAppeal extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Appeal';

    /**
     * Updates Appeal and associated entities
     *
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $appeal = $this->createAppealObject($command);

        $this->getRepo()->save($appeal);
        $result->addMessage('Appeal updated');
        $result->addId('appeal', $appeal->getId());

        return $result;
    }

    /**
     * Update the appeal object
     *
     * @param Cmd $command
     * @return Appeal
     */
    private function createAppealObject(Cmd $command)
    {
        $appeal = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $appeal->setAppealNo($command->getAppealNo());

        if ($command->getDeadlineDate() !== null) {
            $appeal->setDeadlineDate(new \DateTime($command->getDeadlineDate()));
        }

        $appeal->setAppealDate(new \DateTime($command->getAppealDate()));
        $appeal->setReason($this->getRepo()->getRefdataReference($command->getReason()));

        if ($command->getOutlineGround() !== null) {
            $appeal->setOutlineGround($command->getOutlineGround());
        }

        if ($command->getHearingDate() !== null) {
            $appeal->setHearingDate(new \DateTime($command->getHearingDate()));
        }

        if ($command->getDecisionDate() !== null) {
            $appeal->setDecisionDate(new \DateTime($command->getDecisionDate()));
        }

        if ($command->getPapersDueDate() !== null) {
            $appeal->setPapersDueDate(new \DateTime($command->getPapersDueDate()));
        }

        if ($command->getPapersSentDate() !== null) {
            $appeal->setPapersSentDate(new \DateTime($command->getPapersSentDate()));
        }

        if ($command->getComment() !== null) {
            $appeal->setComment($command->getComment());
        }

        if ($command->getOutcome() !== null) {
            $appeal->setOutcome($this->getRepo()->getRefdataReference($command->getOutcome()));
        }

        if ($command->getIsWithdrawn() === 'Y') {
            if ($command->getWithdrawnDate() !== null) {
                $appeal->setWithdrawnDate(new \DateTime($command->getWithdrawnDate()));
            }
        } else {
            $appeal->setWithdrawnDate(null);
        }

        $appeal->setDvsaNotified($command->getDvsaNotified());

        return $appeal;
    }
}
