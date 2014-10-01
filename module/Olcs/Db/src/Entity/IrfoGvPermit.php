<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * IrfoGvPermit Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="irfo_gv_permit",
 *    indexes={
 *        @ORM\Index(name="IDX_DAFAFBC58A75AA28", columns={"irfo_permit_status"}),
 *        @ORM\Index(name="IDX_DAFAFBC592771719", columns={"irfo_gv_permit_type_id"}),
 *        @ORM\Index(name="IDX_DAFAFBC5E02018B7", columns={"withdrawn_reason"}),
 *        @ORM\Index(name="IDX_DAFAFBC59E6B1585", columns={"organisation_id"}),
 *        @ORM\Index(name="IDX_DAFAFBC565CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_DAFAFBC5DE12AB56", columns={"created_by"})
 *    }
 * )
 */
class IrfoGvPermit implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\OrganisationManyToOneAlt1,
        Traits\WithdrawnReasonManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\ExemptionDetails255Field,
        Traits\ExpiryDateField,
        Traits\InForceDateField,
        Traits\IrfoFeeId10Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Irfo gv permit type
     *
     * @var \Olcs\Db\Entity\IrfoGvPermitType
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\IrfoGvPermitType", fetch="LAZY")
     * @ORM\JoinColumn(name="irfo_gv_permit_type_id", referencedColumnName="id", nullable=false)
     */
    protected $irfoGvPermitType;

    /**
     * Irfo permit status
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="irfo_permit_status", referencedColumnName="id", nullable=false)
     */
    protected $irfoPermitStatus;

    /**
     * Is fee exempt
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_fee_exempt", nullable=false)
     */
    protected $isFeeExempt;

    /**
     * No of copies
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="no_of_copies", nullable=false)
     */
    protected $noOfCopies;

    /**
     * Note
     *
     * @var string
     *
     * @ORM\Column(type="string", name="note", length=2000, nullable=true)
     */
    protected $note;

    /**
     * Permit printed
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="permit_printed", nullable=false)
     */
    protected $permitPrinted;

    /**
     * Year required
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="year_required", nullable=true)
     */
    protected $yearRequired;

    /**
     * Set the irfo gv permit type
     *
     * @param \Olcs\Db\Entity\IrfoGvPermitType $irfoGvPermitType
     * @return IrfoGvPermit
     */
    public function setIrfoGvPermitType($irfoGvPermitType)
    {
        $this->irfoGvPermitType = $irfoGvPermitType;

        return $this;
    }

    /**
     * Get the irfo gv permit type
     *
     * @return \Olcs\Db\Entity\IrfoGvPermitType
     */
    public function getIrfoGvPermitType()
    {
        return $this->irfoGvPermitType;
    }

    /**
     * Set the irfo permit status
     *
     * @param \Olcs\Db\Entity\RefData $irfoPermitStatus
     * @return IrfoGvPermit
     */
    public function setIrfoPermitStatus($irfoPermitStatus)
    {
        $this->irfoPermitStatus = $irfoPermitStatus;

        return $this;
    }

    /**
     * Get the irfo permit status
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getIrfoPermitStatus()
    {
        return $this->irfoPermitStatus;
    }

    /**
     * Set the is fee exempt
     *
     * @param string $isFeeExempt
     * @return IrfoGvPermit
     */
    public function setIsFeeExempt($isFeeExempt)
    {
        $this->isFeeExempt = $isFeeExempt;

        return $this;
    }

    /**
     * Get the is fee exempt
     *
     * @return string
     */
    public function getIsFeeExempt()
    {
        return $this->isFeeExempt;
    }

    /**
     * Set the no of copies
     *
     * @param int $noOfCopies
     * @return IrfoGvPermit
     */
    public function setNoOfCopies($noOfCopies)
    {
        $this->noOfCopies = $noOfCopies;

        return $this;
    }

    /**
     * Get the no of copies
     *
     * @return int
     */
    public function getNoOfCopies()
    {
        return $this->noOfCopies;
    }

    /**
     * Set the note
     *
     * @param string $note
     * @return IrfoGvPermit
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get the note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set the permit printed
     *
     * @param string $permitPrinted
     * @return IrfoGvPermit
     */
    public function setPermitPrinted($permitPrinted)
    {
        $this->permitPrinted = $permitPrinted;

        return $this;
    }

    /**
     * Get the permit printed
     *
     * @return string
     */
    public function getPermitPrinted()
    {
        return $this->permitPrinted;
    }

    /**
     * Set the year required
     *
     * @param int $yearRequired
     * @return IrfoGvPermit
     */
    public function setYearRequired($yearRequired)
    {
        $this->yearRequired = $yearRequired;

        return $this;
    }

    /**
     * Get the year required
     *
     * @return int
     */
    public function getYearRequired()
    {
        return $this->yearRequired;
    }
}
