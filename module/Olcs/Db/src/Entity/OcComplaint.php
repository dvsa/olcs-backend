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
 *        @ORM\Index(name="ix_oc_complaint_complaint_id", columns={"complaint_id"}),
 *        @ORM\Index(name="ix_oc_complaint_operating_centre_id", columns={"operating_centre_id"})
 *    }
 * )
 */
class OcComplaint implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\OlbsKeyField,
        Traits\OperatingCentreManyToOne;

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
