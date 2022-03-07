<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Traits;

use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Derived type of licence params trait
 */
trait DerivedTypeOfLicenceParamsTrait
{
    /**
     * Derive the correct vehicle type ref data for the application/licence based upon information in the command
     *
     * @param CommandInterface $command
     *
     * @return RefData
     */
    public function getDerivedVehicleType(CommandInterface $command)
    {
        $vehicleType = $command->getVehicleType();

        if ($vehicleType == RefData::APP_VEHICLE_TYPE_LGV ||
            $vehicleType == RefData::APP_VEHICLE_TYPE_MIXED
        ) {
            return $this->getRepo()->getRefdataReference($vehicleType);
        }

        $mappings = [
            Licence::LICENCE_CATEGORY_GOODS_VEHICLE => RefData::APP_VEHICLE_TYPE_HGV,
            Licence::LICENCE_CATEGORY_PSV => RefData::APP_VEHICLE_TYPE_PSV,
        ];

        return $this->getRepo()->getRefdataReference(
            $mappings[$this->getDerivedOperatorType($command)->getId()]
        );
    }

    /**
     * Derive the correct operator type ref data for the application/licence based upon information in the command
     *
     * @param CommandInterface $command
     *
     * @return RefData
     */
    public function getDerivedOperatorType(CommandInterface $command)
    {
        if ($command->getNiFlag() !== 'Y') {
            return $this->getRepo()->getRefdataReference($command->getOperatorType());
        }

        return $this->getRepo()->getRefdataReference(Licence::LICENCE_CATEGORY_GOODS_VEHICLE);
    }
}
