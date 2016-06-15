<?php

namespace Dvsa\Olcs\Api\Entity\Pi;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\Pi\PresidingTc as PresidingTcEntity;
use Dvsa\Olcs\Api\Entity\Pi\Pi as PiEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Venue as VenueEntity;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;

/**
 * PiHearing Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="pi_hearing",
 *    indexes={
 *        @ORM\Index(name="ix_pi_hearing_pi_id", columns={"pi_id"}),
 *        @ORM\Index(name="ix_pi_hearing_presiding_tc_id", columns={"presiding_tc_id"}),
 *        @ORM\Index(name="ix_pi_hearing_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_pi_hearing_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_pi_hearing_presided_by_role", columns={"presided_by_role"}),
 *        @ORM\Index(name="ix_pi_hearing_venue_id", columns={"venue_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_pi_hearing_olbs_key_olbs_type", columns={"olbs_key","olbs_type"})
 *    }
 * )
 */
class PiHearing extends AbstractPiHearing
{
    /**
     * @param PiEntity $pi
     * @param PresidingTcEntity $presidingTc
     * @param RefData $presidedByRole
     * @param \DateTime $hearingDate
     * @param VenueEntity|null $venue
     * @param string $venueOther
     * @param int $witnesses
     * @param string $isCancelled
     * @param string $cancelledDate
     * @param string $cancelledReason
     * @param string $isAdjourned
     * @param string $adjournedDate
     * @param string $adjournedReason
     * @param string $details
     */
    public function __construct(
        PiEntity $pi,
        PresidingTcEntity $presidingTc,
        RefData $presidedByRole,
        \DateTime $hearingDate,
        $venue,
        $venueOther,
        $witnesses,
        $isCancelled,
        $cancelledDate,
        $cancelledReason,
        $isAdjourned,
        $adjournedDate,
        $adjournedReason,
        $details
    ) {
        $this->create(
            $pi,
            $presidingTc,
            $presidedByRole,
            $hearingDate,
            $venue,
            $venueOther,
            $witnesses,
            $isCancelled,
            $cancelledDate,
            $cancelledReason,
            $isAdjourned,
            $adjournedDate,
            $adjournedReason,
            $details
        );
    }

    /**
     * @param Pi $pi
     * @param PresidingTc $presidingTc
     * @param RefData $presidedByRole
     * @param \DateTime $hearingDate
     * @param VenueEntity|null $venue
     * @param string $venueOther
     * @param int $witnesses
     * @param string $isCancelled
     * @param string $cancelledDate
     * @param string $cancelledReason
     * @param string $isAdjourned
     * @param string $adjournedDate
     * @param string $adjournedReason
     * @param string $details
     * @throws ForbiddenException
     */
    private function create(
        PiEntity $pi,
        PresidingTcEntity $presidingTc,
        RefData $presidedByRole,
        \DateTime $hearingDate,
        $venue,
        $venueOther,
        $witnesses,
        $isCancelled,
        $cancelledDate,
        $cancelledReason,
        $isAdjourned,
        $adjournedDate,
        $adjournedReason,
        $details
    ) {
        if ($pi->isClosed()) {
            throw new ForbiddenException('Can\'t create a hearing for a closed Pi');
        }

        $this->pi = $pi;
        $this->presidingTc = $presidingTc;
        $this->presidedByRole = $presidedByRole;
        $this->venue = $venue;
        $this->venueOther = $venueOther;
        $this->hearingDate = $hearingDate;
        $this->witnesses = $witnesses;
        $this->details = $details;
        $this->isCancelled = $isCancelled;
        $this->isAdjourned = $isAdjourned;

        $this->processAdjournedAndCancelled(
            $isCancelled,
            $cancelledReason,
            $cancelledDate,
            $isAdjourned,
            $adjournedReason,
            $adjournedDate
        );
    }

    /**
     * @param PresidingTc $presidingTc
     * @param RefData $presidedByRole
     * @param \DateTime $hearingDate
     * @param VenueEntity|null $venue
     * @param string $venueOther
     * @param int $witnesses
     * @param string $isCancelled
     * @param string $cancelledDate
     * @param string $cancelledReason
     * @param string $isAdjourned
     * @param string $adjournedDate
     * @param string $adjournedReason
     * @param string $details
     * @throws ForbiddenException
     */
    public function update(
        PresidingTcEntity $presidingTc,
        RefData $presidedByRole,
        \DateTime $hearingDate,
        $venue,
        $venueOther,
        $witnesses,
        $isCancelled,
        $cancelledDate,
        $cancelledReason,
        $isAdjourned,
        $adjournedDate,
        $adjournedReason,
        $details
    ) {
        if ($this->getPi()->isClosed()) {
            throw new ForbiddenException('Can\'t update a hearing for a closed Pi');
        }

        $this->presidingTc = $presidingTc;
        $this->presidedByRole = $presidedByRole;
        $this->venue = $venue;
        $this->venueOther = $venueOther;
        $this->hearingDate = $hearingDate;
        $this->witnesses = $witnesses;
        $this->details = $details;
        $this->isCancelled = $isCancelled;
        $this->isAdjourned = $isAdjourned;

        $this->processAdjournedAndCancelled(
            $isCancelled,
            $cancelledReason,
            $cancelledDate,
            $isAdjourned,
            $adjournedReason,
            $adjournedDate
        );
    }

    /**
     * Process adjourned and cancelled information
     *
     * @param $isCancelled
     * @param $cancelledReason
     * @param $cancelledDate
     * @param $isAdjourned
     * @param $adjournedReason
     * @param $adjournedDate
     */
    private function processAdjournedAndCancelled(
        $isCancelled,
        $cancelledReason,
        $cancelledDate,
        $isAdjourned,
        $adjournedReason,
        $adjournedDate
    ) {
        if ($isCancelled == 'Y') {
            $this->cancelledDate = $this->processDate($cancelledDate);
            $this->cancelledReason = $cancelledReason;
        } else {
            $this->cancelledReason = null;
            $this->cancelledDate = null;
        }

        if ($isAdjourned == 'Y') {
            $this->adjournedDate = $this->processDate($adjournedDate, \DateTime::ISO8601, false);
            $this->adjournedReason = $adjournedReason;
        } else {
            $this->adjournedReason = null;
            $this->adjournedDate = null;
        }
    }

    /**
     * @param string $date
     * @param string $format
     * @param bool $zeroTime
     * @return \DateTime|null
     */
    private function processDate($date, $format = 'Y-m-d', $zeroTime = true)
    {
        $dateTime = \DateTime::createFromFormat($format, $date);

        if (!$dateTime instanceof \DateTime) {
            return null;
        }

        if ($zeroTime) {
            $dateTime->setTime(0, 0, 0);
        }

        return $dateTime;
    }
}
