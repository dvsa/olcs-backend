<?php

/**
 * Reprint Disc
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Vehicle;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Vehicle\GoodsDisc;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle;
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

        $count = 0;

        foreach ($command->getIds() as $id) {

            /** @var LicenceVehicle $licenceVehicle */
            $licenceVehicle = $this->getRepo()->getReference(LicenceVehicle::class, $id);

            $goodsDisc = new GoodsDisc($licenceVehicle);
            $goodsDisc->setIsCopy('Y');

            $this->getRepo()->save($goodsDisc);
            $count++;
        }

        $result->addMessage($count . ' Discs(s) created');

        return $result;
    }
}
