<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Processing\Note;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Repository\Note as NoteRepository;
use Dvsa\Olcs\Transfer\Command\Processing\Note\Update as UpdateCommand;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Exception;
use Doctrine\ORM\Query;

use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * Update a Note
 */
final class Update extends CreateUpdateAbstract
{
    /**
     * @param UpdateCommand $command
     * @throws Exception
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var NoteRepository $repo */
        $repo = $this->getRepo();

        try {

            $repo->beginTransaction();

            $note = $this->getNoteEntity($command);
            $note->setId($command->getId());
            $note->setVersion($command->getVersion());
            $note->setComment($command->getComment());

            $this->getRepo()->save($note);

            $this->getRepo()->commit();

            $result->addId('note', $note->getId());
            $result->addMessage('Note updated');

            return $result;

        } catch (\Exception $ex) {

            $this->getRepo()->rollback();
            throw $ex;
        }
    }

    /**
     * @param CommandInterface $command
     * @return Entity\Note\Note
     */
    protected function retrieveEntity(CommandInterface $command)
    {
        return $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);
    }
}
