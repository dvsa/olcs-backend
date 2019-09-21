<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationPath;
use RuntimeException;

/**
 * IrhpPermitType Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="irhp_permit_type",
 *    indexes={
 *        @ORM\Index(name="irhp_permit_type_ref_data_id_fk", columns={"name"}),
 *        @ORM\Index(name="fk_irhp_permit_type_created_by_user_id", columns={"created_by"}),
 *        @ORM\Index(name="fk_irhp_permit_type_last_modified_by_user_id",
     *     columns={"last_modified_by"})
 *    }
 * )
 */
class IrhpPermitType extends AbstractIrhpPermitType
{
    const IRHP_PERMIT_TYPE_ID_ECMT = 1;
    const IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM = 2;
    const IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL = 3;
    const IRHP_PERMIT_TYPE_ID_BILATERAL = 4;
    const IRHP_PERMIT_TYPE_ID_MULTILATERAL = 5;

    const ALLOCATION_MODE_STANDARD = 'allocation_mode_standard';
    const ALLOCATION_MODE_EMISSIONS_CATEGORIES = 'allocation_mode_emissions_categories';
    const ALLOCATION_MODE_STANDARD_WITH_EXPIRY = 'allocation_mode_standard_expiry';

    const IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL_EXPIRY_INTERVAL = 'P1Y';

    /**
     * Gets calculated values
     *
     * @return array
     */
    public function getCalculatedBundleValues()
    {
        return [
            'isEcmtAnnual' => $this->isEcmtAnnual(),
            'isEcmtShortTerm' => $this->isEcmtShortTerm(),
            'isEcmtRemoval' => $this->isEcmtRemoval(),
            'isBilateral' => $this->isBilateral(),
            'isMultilateral' => $this->isMultilateral(),
            'isApplicationPathEnabled' => $this->isApplicationPathEnabled(),
        ];
    }

    /**
     * Is this ECMT Annual
     *
     * @return bool
     */
    public function isEcmtAnnual()
    {
        return $this->id === self::IRHP_PERMIT_TYPE_ID_ECMT;
    }

    /**
     * Is this ECMT Short Term
     *
     * @return bool
     */
    public function isEcmtShortTerm()
    {
        return $this->id === self::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM;
    }

    /**
     * Is this ECMT Removal
     *
     * @return bool
     */
    public function isEcmtRemoval()
    {
        return $this->id === self::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL;
    }

    /**
     * Is this Bilateral
     *
     * @return bool
     */
    public function isBilateral()
    {
        return $this->id === self::IRHP_PERMIT_TYPE_ID_BILATERAL;
    }

    /**
     * Is this Multilateral
     *
     * @return bool
     */
    public function isMultilateral()
    {
        return $this->id === self::IRHP_PERMIT_TYPE_ID_MULTILATERAL;
    }

    /**
     * Is application path enabled
     *
     * @return bool
     */
    public function isApplicationPathEnabled()
    {
        return $this->isEcmtShortTerm() || $this->isEcmtRemoval();
    }

    /**
     * Get the permit allocation mode used by this permit type
     *
     * @return string
     * @throws RuntimeException
     */
    public function getAllocationMode()
    {
        $mappings = [
            self::IRHP_PERMIT_TYPE_ID_BILATERAL => self::ALLOCATION_MODE_STANDARD,
            self::IRHP_PERMIT_TYPE_ID_MULTILATERAL => self::ALLOCATION_MODE_STANDARD,
            self::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM => self::ALLOCATION_MODE_EMISSIONS_CATEGORIES,
            self::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL => self::ALLOCATION_MODE_STANDARD_WITH_EXPIRY
        ];

        if (isset($mappings[$this->id])) {
            return $mappings[$this->id];
        }

        throw new RuntimeException('No allocation mode set for permit type ' . $this->id);
    }

    /**
     * Get the expiry interval string used by this permit type for DateTime expiry offset from issue_date
     *
     * @return string
     * @throws RuntimeException
     */
    public function getExpiryInterval()
    {
        $mappings = [
            self::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL => self::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL_EXPIRY_INTERVAL
        ];

        if (isset($mappings[$this->id])) {
            return $mappings[$this->id];
        }

        throw new RuntimeException('No expiry interval defined for permit type ' . $this->id);
    }
}
