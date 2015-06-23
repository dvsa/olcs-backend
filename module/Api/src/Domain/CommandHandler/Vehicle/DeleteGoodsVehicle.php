<?php

/**
 * Delete Goods Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Vehicle;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle;
use Dvsa\Olcs\Api\Entity\Vehicle\GoodsDisc;

/**
 * Delete Goods Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class DeleteGoodsVehicle extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'LicenceVehicle';

    protected $extraRepos = ['GoodsDisc'];

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $discs = $vehicles = 0;

        foreach ($command->getIds() as $id) {
            /** @var LicenceVehicle $licenceVehicle */
            $licenceVehicle = $this->getRepo()->fetchById($id);

            if ($licenceVehicle->getRemovalDate() === null) {
                if ($this->ceaseActiveDisc($licenceVehicle)) {
                    $discs++;
                }
                $licenceVehicle->setRemovalDate(new \DateTime());
                $this->getRepo()->save($licenceVehicle);
                $vehicles++;
            }
        }

        $result->addMessage($discs . ' Discs(s) Ceased');
        $result->addMessage($vehicles . ' Vehicle(s) Deleted');

        return $result;
    }

    private function ceaseActiveDisc(LicenceVehicle $licenceVehicle)
    {
        $goodsDiscs = $licenceVehicle->getGoodsDiscs();

        if ($goodsDiscs->count() > 0) {
            /** @var GoodsDisc $activeDisc */
            $activeDisc = $goodsDiscs->first();

            if ($activeDisc->getCeasedDate() === null) {
                $activeDisc->setCeasedDate(new \DateTime());
                $this->getRepo('GoodsDisc')->save($activeDisc);
                return true;
            }
        }

        return false;
    }
}
