<?php

namespace Dvsa\Olcs\Api\Entity\OperatingCentre;

use Doctrine\ORM\Mapping as ORM;

/**
 * OcComplaint Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\Table(name="oc_complaint",
 *    indexes={
 *        @ORM\Index(name="ix_oc_complaint_complaint_id", columns={"complaint_id"}),
 *        @ORM\Index(name="ix_oc_complaint_operating_centre_id", columns={"operating_centre_id"})
 *    }
 * )
 */
abstract class AbstractOcComplaint
{

    /**
     * Complaint
     *
     * @var \Dvsa\Olcs\Api\Entity\Cases\Complaint
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Cases\Complaint", inversedBy="ocComplaints")
     * @ORM\JoinColumn(name="complaint_id", referencedColumnName="id", nullable=false)
     */
    protected $complaint;

    /**
     * Identifier - Id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * Olbs key
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="olbs_key", nullable=true)
     */
    protected $olbsKey;

    /**
     * Operating centre
     *
     * @var \Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre", inversedBy="ocComplaints")
     * @ORM\JoinColumn(name="operating_centre_id", referencedColumnName="id", nullable=false)
     */
    protected $operatingCentre;

    /**
     * Set the complaint
     *
     * @param \Dvsa\Olcs\Api\Entity\Cases\Complaint $complaint
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
     * @return \Dvsa\Olcs\Api\Entity\Cases\Complaint
     */
    public function getComplaint()
    {
        return $this->complaint;
    }

    /**
     * Set the id
     *
     * @param int $id
     * @return OcComplaint
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the olbs key
     *
     * @param int $olbsKey
     * @return OcComplaint
     */
    public function setOlbsKey($olbsKey)
    {
        $this->olbsKey = $olbsKey;

        return $this;
    }

    /**
     * Get the olbs key
     *
     * @return int
     */
    public function getOlbsKey()
    {
        return $this->olbsKey;
    }

    /**
     * Set the operating centre
     *
     * @param \Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre $operatingCentre
     * @return OcComplaint
     */
    public function setOperatingCentre($operatingCentre)
    {
        $this->operatingCentre = $operatingCentre;

        return $this;
    }

    /**
     * Get the operating centre
     *
     * @return \Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre
     */
    public function getOperatingCentre()
    {
        return $this->operatingCentre;
    }


}
