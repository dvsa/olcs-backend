<?php

/**
 * Update SlaTargetDate
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Sla;

use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\System\SlaTargetDate as SlaTargetDateEntity;
use Dvsa\Olcs\Transfer\Command\Sla\UpdateSlaTargetDate as Cmd;

/**
 * Update SlaTargetDate
 */
final class UpdateSlaTargetDate extends AbstractCommandHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'SlaTargetDate';

    protected $extraRepos = ['document'];

    /**
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var SlaTargetDateEntity $slaTargetDateEntity */
        $slaTargetDateEntity = $this->UpdateSlaTargetDate($command);

        $this->getRepo()->save($slaTargetDateEntity);

        $result = new Result();
        $result->addId('SlaTargetDate', $slaTargetDateEntity->getId());
        $result->addMessage('SlaTargetDate Updated successfully');

        return $result;
    }

    /**
     * @param Cmd $command
     * @return SlaTargetDateEntity
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function UpdateSlaTargetDate(CommandInterface $command)
    {
        $slaTargetDateEntity = $this->getRepo()->fetchUsingEntityIdAndType(
            $command->getEntityType(),
            $command->getEntityId(),
            $command->getVersion()
        );

        $slaTargetDateEntity->setAgreedDate($command->getAgreedDate());
        $slaTargetDateEntity->setTargetDate($command->getTargetDate());
        $slaTargetDateEntity->setSentDate($command->getSentDate());
        $slaTargetDateEntity->setUnderDelegation($command->getUnderDelegation());
        $slaTargetDateEntity->setNotes($command->getNotes());

        $currentUser = $this->getCurrentUser();
        $slaTargetDateEntity->setLastModifiedOn(new \DateTime('now'));
        $slaTargetDateEntity->setLastModifiedBy($currentUser);

        return $slaTargetDateEntity;
    }

    /**
     * Fetches the required entity that the SLA target date relates to.
     *
     * @param Cmd $command
     * @return mixed
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function fetchEntity(Cmd $command)
    {
        return $this->getRepo($command->getEntityType())->fetchById($command->getEntityId());
    }
}
