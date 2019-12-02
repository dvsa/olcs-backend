<?php

/**
 * Irhp stock duplicate stock check trait
 */

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitStock;

use DateTime;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationPathGroup as ApplicationPathGroupEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
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
    public function duplicateStockCheck($command)
    {
        $editingId = method_exists($command, 'getId') ? $command->getId() : 0;

        // Stocks for bilateral type are permitted to share shame type ID and validFrom/validTo
        if ((int) $command->getIrhpPermitType() !== IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL) {
            $existingStock = $this->getRepo('IrhpPermitStock')
                ->getPermitStockCountByTypeDate(
                    $command->getIrhpPermitType(),
                    $command->getValidFrom(),
                    $command->getValidTo(),
                    $editingId
                );

            if ($existingStock > 0) {
                throw new ValidationException(['You cannot create a duplicate stock']);
            }
        }
    }

    /**
     * Performs validation on provided validity dates.
     *
     * @param $command
     * @throws ValidationException
     */
    public function validityPeriodValidation($command)
    {
        if ((int) $command->getIrhpPermitType() !== IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL) {
            if (is_null($command->getValidFrom()) || is_null($command->getValidTo())) {
                throw new ValidationException(['This permit type requires you specify a Validity start and end date']);
            }

            $validFrom = new DateTime($command->getValidFrom());
            $validTo = new DateTime($command->getValidTo());
            $now = new DateTime();

            if ($validTo < $validFrom) {
                throw new ValidationException(['Validity Period End Date must be equal to or later than Validity Period Start Date']);
            }

            if ($validTo < $now) {
                throw new ValidationException(['Validity Period End date should be today or in the future']);
            }
        }
    }

    /**
     * Common ref-data and other reference resolution used in Create/Update handlers
     *
     * @param $command
     * @return array
     * @throws ValidationException
     */
    public function resolveReferences($command)
    {
        $references = [];
        $references['irhpPermitType'] = $this->getRepo('IrhpPermitStock')->getReference(IrhpPermitTypeEntity::class, $command->getIrhpPermitType());

        $references['country'] = null;
        if ($command->getIrhpPermitType() === IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL) {
            $references['country'] = $this->getRepo('IrhpPermitStock')->getReference(Country::class, $command->getCountry());
        }

        $references['applicationPathGroup'] = null;
        if (method_exists($command, 'getApplicationPathGroup')) {
            $references['applicationPathGroup'] = $this->getRepo('IrhpPermitStock')->getReference(ApplicationPathGroupEntity::class, $command->getApplicationPathGroup());
        }
        return $references;
    }
}
