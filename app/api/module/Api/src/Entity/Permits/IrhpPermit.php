<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * IrhpPermit Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="irhp_permit",
 *    indexes={
 *        @ORM\Index(name="fk_irhp_permits_irhp_permit_range_idx", columns={"irhp_permit_range_id"}),
 *        @ORM\Index(name="fk_irhp_permits_irhp_permit_application1_idx",
     *     columns={"irhp_permit_application_id"}),
 *        @ORM\Index(name="fk_irhp_permits_irhp_candidate_permit1_idx",
     *     columns={"irhp_candidate_permit_id"}),
 *        @ORM\Index(name="fk_irhp_permit_created_by_user_id", columns={"created_by"}),
 *        @ORM\Index(name="fk_irhp_permit_last_modified_by_user_id", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_irhp_permit_status_ref_data_id", columns={"status"})
 *    }
 * )
 */
class IrhpPermit extends AbstractIrhpPermit
{
    const STATUS_PENDING            = 'irhp_permit_pending';
    const STATUS_AWAITING_PRINTING  = 'irhp_permit_awaiting_printing';
    const STATUS_PRINTING           = 'irhp_permit_printing';
    const STATUS_PRINTED            = 'irhp_permit_printed';
    const STATUS_ERROR              = 'irhp_permit_error';

    /**
     * Create new IrhpPermit
     *
     * @param IrhpCandidatePermit   $irhpCandidatePermit
     * @param DateTime              $issueDate
     * @param RefData               $status
     * @param int                   $permitNumber
     *
     * @return IrhpPermit
     */
    public static function createNew(
        IrhpCandidatePermit $irhpCandidatePermit,
        DateTime $issueDate,
        RefData $status,
        $permitNumber
    ) {
        $irhpPermit = new self();
        $irhpPermit->irhpCandidatePermit = $irhpCandidatePermit;
        $irhpPermit->irhpPermitApplication = $irhpCandidatePermit->getIrhpPermitApplication();
        $irhpPermit->irhpPermitRange = $irhpCandidatePermit->getIrhpPermitRange();
        $irhpPermit->issueDate = $issueDate;
        $irhpPermit->status = $status;
        $irhpPermit->permitNumber = $permitNumber;

        return $irhpPermit;
    }

    /**
     * Get the permit number with prefix
     *
     * @return string
     */
    public function getPermitNumberWithPrefix()
    {
        return sprintf('%s%05d', $this->getIrhpPermitRange()->getPrefix(), $this->getPermitNumber());
    }

    /**
     * Calculated values to be added to a bundle
     *
     * @return array
     */
    public function getCalculatedBundleValues()
    {
        return [
            'permitNumberWithPrefix' => $this->getPermitNumberWithPrefix()
        ];
    }

    /**
     * Proceed to awaiting printing
     *
     * @param RefData $status Status
     *
     * @return void
     * @throws ForbiddenException
     */
    public function proceedToAwaitingPrinting(RefData $status)
    {
        if (!$this->isPending() && !$this->hasError()) {
            throw new ForbiddenException(
                sprintf(
                    'The permit is not in the correct state to proceed to awaiting printing (%s)',
                    $this->status->getId()
                )
            );
        }

        $this->status = $status;
    }

    /**
     * Is pending
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status->getId() === self::STATUS_PENDING;
    }

    /**
     * Has error
     *
     * @return bool
     */
    public function hasError()
    {
        return $this->status->getId() === self::STATUS_ERROR;
    }
}
