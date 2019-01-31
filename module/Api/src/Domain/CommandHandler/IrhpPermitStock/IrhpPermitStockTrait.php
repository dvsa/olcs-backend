<?php

/**
 * Irhp stock duplicate stock check trait
 */

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitStock;

use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType as IrhpPermitTypeEntity;

trait IrhpPermitStockTrait
{
    /**
     * Throws a validation error if any duplicate stock (same type and validity period) is being added for any
     * type other than Annual Bilateral
     *
     * @param $command
     * @return void
     * @throws ValidationException
     */
    protected function duplicateStockCheck($command)
    {
        // Stocks for bilateral type are permitted to share shame type ID and validFrom/validTo
        if ((int) $command->getIrhpPermitType() !== IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL) {
            $existingStock = $this->getRepo('IrhpPermitStock')
                ->getPermitStockCountByTypeDate(
                    $command->getIrhpPermitType(),
                    $command->getValidFrom(),
                    $command->getValidTo()
                );

            if ($existingStock > 0) {
                throw new ValidationException(['You cannot create a duplicate stock']);
            }
        }
    }
}
