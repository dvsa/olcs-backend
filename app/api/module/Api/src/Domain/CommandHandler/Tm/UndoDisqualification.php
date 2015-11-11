<?php

/**
 * Undo disqualification
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Tm;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager;

/**
 * Undo disqualification
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class UndoDisqualification extends AbstractCommandHandler
{
    protected $repoServiceName = 'TransportManager';

    public function handleCommand(CommandInterface $command)
    {
        $transportManager = $this->getRepo()->fetchUsingId($command);

        $result = new Result();
        if ($transportManager->getTmStatus()->getId() === TransportManager::TRANSPORT_MANAGER_STATUS_DISQUALIFIED) {
            $transportManager->setTmStatus(
                $this->getRepo()->getRefdataReference(TransportManager::TRANSPORT_MANAGER_STATUS_CURRENT)
            );
            $transportManager->setDisqualificationTmCaseId(null);
            $this->getRepo()->save($transportManager);
            $result->addMessage('Disqualification removed');
        } else {
            $result->addMessage('TM status is not disqualified');
        }

        return $result;
    }
}
