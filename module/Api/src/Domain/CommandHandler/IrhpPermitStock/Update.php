<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitStock;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\System\IrhpPermitStock as StockEntity;
use Dvsa\Olcs\Transfer\Command\IrhpPermitStock\Update as UpdateStockCmd;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as PermitStockRepo;
use Olcs\Logging\Log\Logger;

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
         * @var UpdateStockCmd $command
         * @var StockEntity $stock
         * @var PermitStockRepo $repo
         */
        $repo = $this->getRepo();
        $stock = $repo->fetchUsingId($command);
        Logger::err(print_r($stock, true));
        $permitType = $this->getRepo('IrhpPermitType')->fetchById($command->getPermitType());


        $stock->update(
            $permitType,
            $command->getValidFrom(),
            $command->getValidTo(),
            $command->getQuota()
        );

        $repo->save($stock);

        $this->result->addId('Irhp Permit Stock', $stock->getId());
        $this->result->addMessage("Irhp Permit Stock '{$stock->getId()}' updated");

        return $this->result;
    }
}
