<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Command\Permits\GeneratePermit;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * PrintPermits
 */
final class PrintPermits extends AbstractCommandHandler
{
    /**
     * @param CommandInterface $command
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    public function handleCommand(CommandInterface $command)
    {
        return $this->generateDocument($command->getIds());
    }

    /**
     * @param array $ids
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    public function generateDocument(array $ids)
    {
        return $this->handleSideEffect(
            GeneratePermit::create(['ids' => $ids])
        );
    }
}
