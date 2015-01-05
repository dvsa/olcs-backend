<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * OcComplaint Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="oc_complaint",
 *    indexes={
 *        @ORM\Index(name="fk_oc_complaint_complaint1_idx", columns={"complaint_id"}),
 *        @ORM\Index(name="fk_oc_complaint_operating_centre1_idx", columns={"operating_centre_id"}),
 *        @ORM\Index(name="fk_oc_complaint_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_oc_complaint_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class OcComplaint implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\OlbsKeyField,
        Traits\OperatingCentreManyToOne,
        Traits\CustomVersionField;

    /**
     * Complaint
     *
     * @var \Olcs\Db\Entity\Complaint
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Complaint", inversedBy="ocComplaints")
     * @ORM\JoinColumn(name="complaint_id", referencedColumnName="id", nullable=false)
     */
    protected $complaint;

    /**
     * Set the complaint
     *
     * @param \Olcs\Db\Entity\Complaint $complaint
     * @return OcComplaint
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
