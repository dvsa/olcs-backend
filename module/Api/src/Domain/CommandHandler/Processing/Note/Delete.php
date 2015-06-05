<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Processing\Note;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Repository\Note as NoteRepository;
use Dvsa\Olcs\Transfer\Command\Processing\Note\Delete as DeleteCommand;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Exception;
use Doctrine\ORM\Query;

use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * Delete a Note
 */
final class Delete extends AbstractCommandHandler
{
    protected $repoServiceName = 'Note';

    /**
     * @param DeleteCommand $command
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

            $note = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);
            $this->getRepo()->delete($note);

            $this->getRepo()->commit();

            $result->addMessage('Note deleted');

            return $result;

        } catch (\Exception $ex) {

            $this->getRepo()->rollback();
            throw $ex;
        }
    }
}
