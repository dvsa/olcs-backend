<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\TransportManagerApplication;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;

/**
 * Submit
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class Submit extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'TransportManagerApplication';

    public function handleCommand(CommandInterface $command)
    {
        /* @var $tma TransportManagerApplication */
        if ($command->getVersion()) {
            $tma = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());
        } else {
            $tma = $this->getRepo()->fetchUsingId($command);
        }

        // next status depends on whether TM is the owner
        $nextStatus = ($tma->getIsOwner() === 'Y') ? TransportManagerApplication::STATUS_OPERATOR_SIGNED :
            TransportManagerApplication::STATUS_TM_SIGNED;
        $tma->setTmApplicationStatus($this->getRepo()->getRefdataReference($nextStatus));
        $this->getRepo()->save($tma);

        $this->result->addMessage("Transport Manager Application ID {$tma->getId()} submitted");

        return $this->result;
    }
}
