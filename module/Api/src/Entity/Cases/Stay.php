<?php

namespace Dvsa\Olcs\Api\Entity\Cases;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * Stay Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="stay",
 *    indexes={
 *        @ORM\Index(name="ix_stay_case_id", columns={"case_id"}),
 *        @ORM\Index(name="ix_stay_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_stay_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_stay_outcome", columns={"outcome"}),
 *        @ORM\Index(name="ix_stay_stay_type", columns={"stay_type"})
 *    }
 * )
 */
class Stay extends AbstractStay
{
    public function __construct(Cases $case, RefData $stayType)
    {
        $this->setCase($case);
        $this->setStayType($stayType);
    }

    /**
     * Is the stay outstanding. Dealt with if outcome or decision date set. Or if its been withdrawn.
     *
     * @return bool
     */
    public function isOutstanding()
    {
        // Stay is considered completed if it is withdrawn or if it has a decision date and outcome set
        return !empty($this->getWithdrawnDate()) ||
        (!empty($this->getDecisionDate()) && !empty($this->getOutcome()));
    }
}
