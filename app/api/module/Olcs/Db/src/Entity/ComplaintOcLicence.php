<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * ComplaintOcLicence Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="complaint_oc_licence",
 *    indexes={
 *        @ORM\Index(name="IDX_E14F2C0AEDAE188E", columns={"complaint_id"}),
 *        @ORM\Index(name="IDX_E14F2C0A65CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_E14F2C0ADE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_E14F2C0A35382CCB", columns={"operating_centre_id"}),
 *        @ORM\Index(name="IDX_E14F2C0A26EF07C9", columns={"licence_id"})
 *    }
 * )
 */
class ComplaintOcLicence implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\OperatingCentreManyToOne,
        Traits\LicenceManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Complaint
     *
     * @var \Olcs\Db\Entity\Complaint
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Complaint", fetch="LAZY")
     * @ORM\JoinColumn(name="complaint_id", referencedColumnName="id", nullable=false)
     */
    protected $complaint;

    /**
     * Set the complaint
     *
     * @param \Olcs\Db\Entity\Complaint $complaint
     * @return ComplaintOcLicence
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
