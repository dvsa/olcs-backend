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
 *    }
 * )
 */
class ComplaintCase implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Identifier - Case
     *
     * @var \Olcs\Db\Entity\Cases
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Olcs\Db\Entity\Cases")
     * @ORM\JoinColumn(name="case_id", referencedColumnName="id")
     */
    protected $case;

    /**
     * Identifier - Complaint
     *
     * @var \Olcs\Db\Entity\Complaint
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Olcs\Db\Entity\Complaint")
     * @ORM\JoinColumn(name="complaint_id", referencedColumnName="id")
     */
    protected $complaint;

    /**
     * Set the case
     *
     * @param \Olcs\Db\Entity\Cases $case
     * @return \Olcs\Db\Entity\ComplaintCase
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

    /**
     * Set the complaint
     *
     * @param \Olcs\Db\Entity\Complaint $complaint
     * @return \Olcs\Db\Entity\ComplaintCase
     */
    public function setComplaint($complaint)
    {
        $this->complaint = $complaint;

        return $this;
    }

    /**
     * Get the complaint
     *
     * @return \Olcs\Db\Entity\Complaint
     */
    public function getComplaint()
    {
        return $this->complaint;
    }
}
