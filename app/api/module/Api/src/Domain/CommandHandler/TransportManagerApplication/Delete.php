<?php

/**
 * Delete a Transport Manager Application
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\TransportManagerApplication;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;

/**
 * Delete a Transport Manager Application
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class Delete extends AbstractCommandHandler
{
    protected $repoServiceName = 'TransportManagerApplication';

    public function handleCommand(CommandInterface $command)
    {
        try {
            $result = new Result();

            $this->getRepo()->beginTransaction();

            foreach ($command->getIds() as $tmaId) {
                /* @var $tma TransportManagerApplication */
                $tma = $this->getRepo()->fetchById($tmaId);
                $this->getRepo()->delete($tma);
                $result->addMessage("Transport Manager Application ID {$tmaId} deleted");
            }

            $this->getRepo()->commit();

            return $result;
        } catch (\Exception $ex) {
            $this->getRepo()->rollback();

            throw $ex;
        }
    }
}
