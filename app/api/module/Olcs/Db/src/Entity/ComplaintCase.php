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
 *        @ORM\Index(name="IDX_A7094FAFEDAE188E", columns={"complaint_id"}),
 *        @ORM\Index(name="IDX_A7094FAFCF10D4F5", columns={"case_id"}),
 *        @ORM\Index(name="IDX_A7094FAFDE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_A7094FAF65CF370E", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="complaint_case_unique", columns={"case_id","complaint_id"})
 *    }
 * )
 */
class ComplaintCase implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Complaint
     *
     * @var \Olcs\Db\Entity\Complaint
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Complaint", fetch="LAZY", cascade={"persist","remove"})
     * @ORM\JoinColumn(name="complaint_id", referencedColumnName="id", nullable=false)
     */
    protected $complaint;

    /**
     * Case
     *
     * @var \Olcs\Db\Entity\Cases
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Cases", fetch="LAZY", cascade={"persist"}, inversedBy="complaintCases")
     * @ORM\JoinColumn(name="case_id", referencedColumnName="id", nullable=false)
     */
    protected $case;

    /**
     * Set the complaint
     *
     * @param \Olcs\Db\Entity\Complaint $complaint
     * @return ComplaintCase
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
