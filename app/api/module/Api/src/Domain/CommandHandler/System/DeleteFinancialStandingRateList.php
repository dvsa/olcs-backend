<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\System;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Delete a list of FinancialStandingRates
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class DeleteFinancialStandingRateList extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'FinancialStandingRate';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        foreach ($command->getIds() as $rateId) {
            /* @var $rate \Dvsa\Olcs\Api\Entity\System\FinancialStandingRate */
            $rate = $this->getRepo()->fetchById($rateId);
            $this->getRepo()->delete($rate);

            $result->addMessage("Financial Standing Rate ID {$rateId} deleted");
        }

        return $result;
    }
}
