<?php

/**
 * Cancel Irfo Psv Auth Fees
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Fee;

use Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee as CancelFeeCommand;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\System\RefData as RefDataEntity;

/**
 * Cancel Irfo Psv Auth Fees
 */
final class CancelIrfoPsvAuthFees extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Fee';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $fees = $this->getRepo()->fetchFeesByIrfoPsvAuthId($command->getId());

        /* @var $fee \Dvsa\Olcs\Api\Entity\Fee\Fee */
        foreach ($fees as $fee) {
            if ($fee->getFeeType()->getFeeType() !== RefDataEntity::FEE_TYPE_IRFOPSVAPP) {
                $result->merge(
                    $this->getCommandHandler()->handleCommand(
                        CancelFeeCommand::create(['id' => $fee->getId()])
                    )
                );
            }
        }

        $result->addMessage('IRFO PSV Auth fees cancelled successfully');

        return $result;
    }
}
