<?php

/**
 * CeaseGoodsDiscs.php
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Discs;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Class CeaseGoodsDiscs
 *
 * 'Cease' the goods discs on a licence.
 *
 * @package Dvsa\Olcs\Api\Domain\CommandHandler\Licence
 */
final class CeaseGoodsDiscs extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'GoodsDisc';

    public function handleCommand(CommandInterface $command)
    {
        $licenceVehicles = $command->getLicenceVehicles();

        if (!empty($licenceVehicles)) {
            foreach ($licenceVehicles as $licenceVehicle) {
                $goodsDiscs = $licenceVehicle->getGoodsDiscs();
                if (!empty($goodsDiscs)) {
                    foreach ($goodsDiscs as $disc) {
                        if (is_null($disc->getCeasedDate())) {
                            $disc->setCeasedDate(new \DateTime());
                        }
                        $disc->setIsInterim(false);
                        $this->getRepo()->save($disc);
                    }
                }
            }
        }

        $result = new Result();
        $result->addMessage('Ceased discs for licence.');

        return $result;
    }
}
