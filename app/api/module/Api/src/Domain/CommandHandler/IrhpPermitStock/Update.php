<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitStock;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock as StockEntity;
use Dvsa\Olcs\Transfer\Command\IrhpPermitStock\Update as UpdateStockCmd;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock;

/**
 * Update an IRHP Permit Stock
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
final class Update extends AbstractCommandHandler
{
    protected $repoServiceName = 'IrhpPermitStock';
    protected $extraRepos = ['IrhpPermitType'];

    public function handleCommand(CommandInterface $command): Result
    {
        /**
         * @var IrhpPermitStock $command
         * @var StockEntity $stock
         * @var PermitStockRepo $repo
         */
        $stock = $this->getRepo()->fetchUsingId($command);

        $permitType = $this->getRepo('IrhpPermitType')->fetchById($command->getPermitType());
        $stock->update(
            $permitType,
            $command->getValidFrom(),
            $command->getValidTo(),
            $command->getInitialStock()
        );

        $this->getRepo()->save($stock);

        $this->result->addId('Irhp Permit Stock', $stock->getId());
        $this->result->addMessage("Irhp Permit Stock '{$stock->getId()}' updated");

        return $this->result;
    }
}
