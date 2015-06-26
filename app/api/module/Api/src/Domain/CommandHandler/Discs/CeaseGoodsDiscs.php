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
        /** @var \Dvsa\Olcs\Api\Entity\Licence\Licence $licence */
        $licence = $command->getLicence();

        $licenceVehicles = $licence->getLicenceVehicles();

        foreach ($licenceVehicles as $licenceVehicle) {
            foreach ($licenceVehicle->getGoodsDiscs() as $disc) {
                $disc->setCeasedDate(new \DateTime());
                $this->getRepo()->save($disc);
            }
        }

        $result = new Result();
        $result->addMessage('Ceased discs for licence.');

        return $result;
    }
}
