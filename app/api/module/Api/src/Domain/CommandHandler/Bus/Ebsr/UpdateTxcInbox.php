<?php

/**
 * Update TxcInbox
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Bus\Ebsr;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Ebsr\TxcInbox;
use Dvsa\Olcs\Transfer\Command\Bus\Ebsr\UpdateTxcInbox as UpdateTxcInboxCmd;

/**
 * Update TxcInbox
 */
final class UpdateTxcInbox extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'TxcInbox';

    /**
     * @param CommandInterface $command
     * @return Result
     * @throws \Exception
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var UpdateTxcInboxCmd $command */

        $result = new Result();

        $txcInboxRecords = $this->getRepo()->fetchByIds($command->getIds(), Query::HYDRATE_OBJECT);

        $count = 0;
        /** @var TxcInbox $txcInbox */
        foreach ($txcInboxRecords as $txcInbox) {
            $result->merge($this->updateFileRead($txcInbox));
            $count++;
        }

        $result->addMessage($count . ' records updated');

        return $result;
    }

    /**
     * @param TxcInbox $txcInbox
     * @return Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function updateFileRead(TxcInbox $txcInbox)
    {
        $result = new Result();
        $txcInbox->setFileRead('Y');
        $this->getRepo()->save($txcInbox);

        return $result;
    }
}
