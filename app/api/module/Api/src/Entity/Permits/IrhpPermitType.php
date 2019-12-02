<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Doctrine\ORM\Mapping as ORM;
use DateInterval;
use DateTime;
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
     * Can this permit type have apply for more than one stock on a single application?
     *
     * @return bool
     */
    public function isMultiStock(): bool
    {
        return $this->isMultilateral() || $this->isBilateral();
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
     * Generate an expiry date for this permit type using the supplied date time as an issue date
     *
     * @param DateTime $issueDateTime
     *
     * @return DateTime
     */
    public function generateExpiryDate(DateTime $issueDateTime)
    {
        if (!$this->isEcmtRemoval()) {
            throw new RuntimeException('Unable to generate an expiry date for permit type ' . $this->id);
        }

        $expiryDateTime = clone $issueDateTime;
        $expiryDateTime->add(new DateInterval('P1Y'));
        $expiryDateTime->sub(new DateInterval('P1D'));

        return $expiryDateTime;
    }
}
