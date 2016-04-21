<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Audit;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Abstract Read Audit
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractReadAudit extends AbstractCommandHandler implements TransactionedInterface, AuthAwareInterface
{
    use AuthAwareTrait;

    protected $recordClass;

    protected $entityRepo;

    public function handleCommand(CommandInterface $command)
    {
        if ($this->doesRecordExist($command)) {
            $this->result->addMessage('Audit record exists');
            return $this->result;
        }

        $this->createRecord($command);

        $this->result->addMessage('Audit record created');
        return $this->result;
    }

    /**
     * Grab an entity by id
     *
     * @param $id
     * @return mixed
     */
    protected function getEntity($id)
    {
        $this->extraRepos[] = $this->entityRepo;

        return $this->getRepo($this->entityRepo)->fetchById($id);
    }

    protected function doesRecordExist($command)
    {
        $userId = $this->getCurrentUser()->getId();

        /** @var \Dvsa\Olcs\Api\Domain\Repository\AbstractReadAudit $repo */
        $repo = $this->getRepo();

        return (null !== $repo->fetchOne($userId, $command->getId()));
    }

    protected function createRecord($command)
    {
        $className = $this->recordClass;

        $record = new $className($this->getCurrentUser(), $this->getEntity($command->getId()));
        $this->getRepo()->save($record);
    }
}
