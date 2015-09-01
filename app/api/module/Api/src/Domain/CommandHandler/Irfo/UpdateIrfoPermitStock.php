<?php

/**
 * Update IrfoPermitStock
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Irfo;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Exception;

/**
 * Update IrfoPermitStock
 */
final class UpdateIrfoPermitStock extends AbstractCommandHandler implements TransactionedInterface
{
    const MAX_IDS_COUNT = 100;

    protected $repoServiceName = 'IrfoPermitStock';

    public function handleCommand(CommandInterface $command)
    {
        $ids = $command->getIds();

        if (count($ids) > self::MAX_IDS_COUNT) {
            throw new Exception\ValidationException(
                ['Number of selected records must be less than or equal to '.self::MAX_IDS_COUNT]
            );
        }

        $status = $this->getRepo()->getRefdataReference($command->getStatus());

        $irfoPermitStockList = $this->getRepo()->fetchByIds($ids);

        foreach ($irfoPermitStockList as $irfoPermitStock) {
            $irfoPermitStock->setStatus($status);
            $this->getRepo()->save($irfoPermitStock);
        }

        $result = new Result();
        $result->addMessage('IRFO Permit Stock updated successfully');

        return $result;
    }
}
