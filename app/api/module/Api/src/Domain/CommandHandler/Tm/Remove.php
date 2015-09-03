<?php

/**
 * Remove.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Tm;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Remove transport manager.
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
final class Remove extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'TransportManager';

    public function handleCommand(CommandInterface $command)
    {
        /** @var TransportManager $transportManager */
        $transportManager = $this->getRepo()->fetchById($command->getId());
        $transportManager->setRemovedDate(new \DateTime());

        $this->getRepo()->save($transportManager);

        $result = new Result();
        $result->addMessage('Removed transport manager.');

        return $result;
    }
}
