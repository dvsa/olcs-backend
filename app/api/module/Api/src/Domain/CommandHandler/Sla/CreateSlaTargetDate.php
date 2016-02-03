<?php

/**
 * Create SlaTargetDate
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Sla;

use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\System\SlaTargetDate as SlaTargetDateEntity;
use Dvsa\Olcs\Transfer\Command\Sla\CreateSlaTargetDate as Cmd;

/**
 * Create SlaTargetDate
 */
final class CreateSlaTargetDate extends AbstractCommandHandler implements AuthAwareInterface
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
        $slaTargetDateEntity = $this->createSlaTargetDate($command);

        $this->getRepo()->save($slaTargetDateEntity);

        $result = new Result();
        $result->addId('SlaTargetDate', $slaTargetDateEntity->getId());
        $result->addMessage('SlaTargetDate created successfully');

        return $result;
    }

    /**
     * @param Cmd $command
     * @return SlaTargetDateEntity
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function createSlaTargetDate(CommandInterface $command)
    {
        $entity = $this->fetchEntity($command);

        // @ to-do put entity types into ref data
        $slaTargetDateEntity = new SlaTargetDateEntity(
            $entity,
            new \DateTime($command->getAgreedDate()),
            $command->getUnderDelegation()
        );

        $currentUser = $this->getCurrentUser();

        $slaTargetDateEntity->setCreatedOn(new \DateTime('now'));
        $slaTargetDateEntity->setCreatedBy($currentUser);

        if (!empty($command->getTargetDate())) {
            $slaTargetDateEntity->setTargetDate($command->getTargetDate());
        }

        if (!empty($command->getSentDate())) {
            $slaTargetDateEntity->setSentDate($command->getSentDate());
        }

        $slaTargetDateEntity->setNotes($command->getNotes());

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
