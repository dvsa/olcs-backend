<?php

/**
 * Reprint Disc
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Vehicle;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Vehicle\CreateGoodsDiscs as CreateGoodsDiscsCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Vehicle\CeaseActiveDiscs as CeaseCmd;

/**
 * Reprint Disc
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class ReprintDisc extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'GoodsDisc';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $result->merge($this->proxyCommand($command, CeaseCmd::class));

        $dtoData = $command->getArrayCopy();
        $dtoData['isCopy'] = 'Y';

        $result->merge($this->handleSideEffect(CreateGoodsDiscsCmd::create($dtoData)));

        return $result;
    }
}
