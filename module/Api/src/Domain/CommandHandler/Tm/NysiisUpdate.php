<?php

/**
 * Transport Manager NYSIIS name update
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Tm;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager;

/**
 * Transport Manager NYSIIS name update
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
final class NysiisUpdate extends AbstractCommandHandler implements TransactionedInterface, AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'TransportManager';

    /**
     * Updates the NYSIIS forename and familyName only
     *
     * @param CommandInterface $command
     * @return Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var TransportManager $transportManager */
        $transportManager = $this->getRepo('TransportManager')->fetchById($command->getId());

        $transportManager->setNysiisForename($command->getNysiisForename());
        $transportManager->setNysiisFamilyname($command->getNysiisFamilyname());

        $this->getRepo()->save($transportManager);

        $result->addId('transportManager', $transportManager->getId());
        $result->addMessage('Transport Manager updated successfully');

        return $result;
    }
}
