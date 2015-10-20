<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\TransportManagerApplication;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;

/**
 * OperatorApprove
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class OperatorApprove extends AbstractCommandHandler implements TransactionedInterface
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

        $tma->setTmApplicationStatus(
            $this->getRepo()->getRefdataReference(TransportManagerApplication::STATUS_OPERATOR_SIGNED)
        );
        $this->getRepo()->save($tma);

        $this->result->addMessage("Transport Manager Application ID {$tma->getId()} operator approved");

        return $this->result;
    }
}
