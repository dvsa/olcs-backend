<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * PiReason Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="pi_reason",
 *    indexes={
 *        @ORM\Index(name="fk_case_rec_reason_reason1_idx", columns={"reason_id"}),
 *        @ORM\Index(name="fk_case_reason_pi1_idx", columns={"pi_id"}),
 *        @ORM\Index(name="fk_case_reason_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_case_reason_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class PiReason implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\PiManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Reason
     *
     * @var \Olcs\Db\Entity\Reason
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Reason")
     * @ORM\JoinColumn(name="reason_id", referencedColumnName="id")
     */
    protected $reason;


    /**
     * Set the reason
     *
     * @param \Olcs\Db\Entity\Reason $reason
     * @return PiReason
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get the reason
     *
     * @return \Olcs\Db\Entity\Reason
     */
    public function getReason()
    {
        return $this->reason;
    }

}
