<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Cli\Domain\Command\ResetScoring as ResetScoringCommand;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Reset scoring
 * See https://wiki.i-env.net/display/olcs/Batch+Process%3A+Identify+Successful+Permit+Applications+with+Restricted+Countries
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */

class ResetScoring extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'IrhpCandidatePermit';

    /**
     * Handle command
     *
     * @param CommandInterface|ResetScoringCommand $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $this->getRepo()->resetScoring(
            $command->getStockId()
        );

        $this->result->addMessage('Scoring has been reset');
        return $this->result;
    }
}
