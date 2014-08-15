<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * ComplaintCase Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="complaint_case",
 *    indexes={
 *        @ORM\Index(name="fk_complaint_case_complaint1_idx", columns={"complaint_id"}),
 *        @ORM\Index(name="fk_complaint_case_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_complaint_case_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_A7094FAFCF10D4F5", columns={"case_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="case_id", columns={"case_id","complaint_id"})
 *    }
 * )
 */
class ComplaintCase implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\ComplaintManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Case
     *
     * @var \Olcs\Db\Entity\Cases
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Cases", fetch="LAZY", inversedBy="complaintCases")
     * @ORM\JoinColumn(name="case_id", referencedColumnName="id", nullable=false)
     */
    protected $case;


    /**
     * Set the case
     *
     * @param \Olcs\Db\Entity\Cases $case
     * @return ComplaintCase
     */
    public function setCase($case)
    {
        $this->case = $case;

        return $this;
    }

    /**
     * Get the case
     *
     * @return \Olcs\Db\Entity\Cases
     */
    public function getCase()
    {
        return $this->case;
    }
}
