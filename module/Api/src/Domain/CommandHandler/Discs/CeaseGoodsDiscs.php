<?php

/**
 * Cease Goods Discs
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Discs;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Discs\CeaseGoodsDiscs as Cmd;

/**
 * Cease Goods Discs
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CeaseGoodsDiscs extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'GoodsDisc';

    /**
     * @param Cmd $command
     * @return Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        $this->getRepo()->ceaseDiscsForLicence($command->getLicence());

        $result = new Result();
        $result->addMessage('Ceased discs for licence.');

        return $result;
    }
}
