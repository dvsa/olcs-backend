<?php

/**
 * Create Goods Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle;
use Dvsa\Olcs\Api\Entity\Vehicle\GoodsDisc;
use Dvsa\Olcs\Api\Entity\Vehicle\Vehicle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Domain\Command\Vehicle\CreateGoodsVehicle as VehicleCmd;

/**
 * Create Goods Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateGoodsVehicle extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Licence';

    protected $extraRepos = ['GoodsDisc'];

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var LicenceEntity $licence */
        $licence = $this->getRepo()->fetchUsingId($command);

        $remainingSpaces = $licence->getRemainingSpaces();

        // If we already have enough vehicles
        if ($remainingSpaces < 1) {
            throw new ValidationException(
                [
                    'vrm' => [
                        Vehicle::ERROR_TOO_MANY => 'The number of vehicles will exceed the total authorisation'
                    ]
                ]
            );
        }

        $dtoData = $command->getArrayCopy();
        $dtoData['licence'] = $command->getId();
        if ($command->getSpecifiedDate() === null) {
            $dtoData['specifiedDate'] = date('Y-m-d');
        }

        $vehicleResult = $this->handleSideEffect(VehicleCmd::create($dtoData));
        $result->merge($vehicleResult);

        $licenceVehicleId = $vehicleResult->getId('licenceVehicle');

        // @todo maybe move this to another command
        $goodsDisc = new GoodsDisc();
        $goodsDisc->setLicenceVehicle($this->getRepo()->getReference(LicenceVehicle::class, $licenceVehicleId));
        $goodsDisc->setIsCopy('N');
        $this->getRepo('GoodsDisc')->save($goodsDisc);

        return $result;
    }
}
