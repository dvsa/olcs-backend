<?php

namespace Dvsa\Olcs\Api\Entity\Organisation;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

/**
 * Disqualification Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="disqualification",
 *    indexes={
 *        @ORM\Index(name="ix_disqualification_organisation_id", columns={"organisation_id"}),
 *        @ORM\Index(name="ix_disqualification_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_disqualification_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_disqualification_officer_cd_id", columns={"officer_cd_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_disqualification_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class Disqualification extends AbstractDisqualification
{
    const STATUS_NONE = 'None';
    const STATUS_ACTIVE = 'Active';
    const STATUS_INACTIVE = 'Inactive';

    /**
     * Get the current status of this Disqualification
     *
     * @return string self::STATUS_ACTIVE or self::STATUS_INACTIVE
     */
    public function getStatus()
    {
        // if there is a disqualification record where is_disqualified = 1 and the current date falls between the
        // start date and end date (end_date = start_date + period (months)). The disqualification is also active
        // if is_disqualified = 1 and the start_date is today or in the past and period is NULL or 0
        if ($this->getIsDisqualified() === 'Y') {
            $startDate = new DateTime($this->getStartDate());
            if (empty($this->getPeriod())) {
                // if period is empty make sure endDate is always in the future, set to today + 1 month
                $endDate = new DateTime('+1 month');
            } else {
                $endDate = (new DateTime($this->getStartDate()))
                    ->add(new \DateInterval('P'. (int) $this->getPeriod() .'M'));
            }
            $now = new DateTime('now');
            if ($now->getTimestamp() >= $startDate->getTimestamp() &&
                $now->getTimestamp() <= $endDate->getTimestamp()
                ) {
                return self::STATUS_ACTIVE;
            }
        }
        return self::STATUS_INACTIVE;
    }
}
