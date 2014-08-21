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
 *        @ORM\Index(name="fk_case_rec_pi_reason_pi_reason1_idx", columns={"reason_id"}),
 *        @ORM\Index(name="fk_case_pi_reason_pi_detail1_idx", columns={"pi_id"}),
 *        @ORM\Index(name="fk_case_pi_reason_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_case_pi_reason_user2_idx", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="pi_reason_unique", columns={"pi_id","reason_id"})
 *    }
 * )
 */
class PiReason implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\ReasonManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Pi
     *
     * @var \Olcs\Db\Entity\Pi
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Pi", fetch="LAZY", inversedBy="piReasons")
     * @ORM\JoinColumn(name="pi_id", referencedColumnName="id", nullable=false)
     */
    protected $pi;

    /**
     * Set the pi
     *
     * @param \Olcs\Db\Entity\Pi $pi
     * @return PiReason
     */
    public function setPi($pi)
    {
        $this->pi = $pi;

        return $this;
    }

    /**
     * Get the pi
     *
     * @return \Olcs\Db\Entity\Pi
     */
    public function getPi()
    {
        return $this->pi;
    }
}
