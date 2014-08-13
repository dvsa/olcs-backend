<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Impounding Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="impounding",
 *    indexes={
 *        @ORM\Index(name="fk_impounding_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_impounding_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_impounding_transport_commissioner1_idx", columns={"presiding_tc_id"}),
 *        @ORM\Index(name="fk_impounding_ref_data1_idx", columns={"outcome"}),
 *        @ORM\Index(name="fk_impounding_ref_data2_idx", columns={"impounding_type"}),
 *        @ORM\Index(name="fk_impounding_cases1_idx", columns={"case_id"}),
 *        @ORM\Index(name="fk_impounding_pi_venue1_idx", columns={"pi_venue_id"})
 *    }
 * )
 */
class Impounding implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CaseManyToOne,
        Traits\OutcomeManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\PresidingTcManyToOne,
        Traits\CreatedByManyToOne,
        Traits\HearingDateField,
        Traits\Notes4000Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Pi venue
     *
     * @var \Olcs\Db\Entity\PiVenue
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\PiVenue", fetch="LAZY")
     * @ORM\JoinColumn(name="pi_venue_id", referencedColumnName="id")
     */
    protected $piVenue;

    /**
     * Impounding type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="impounding_type", referencedColumnName="id")
     */
    protected $impoundingType;

    /**
     * Application receipt date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="application_receipt_date", nullable=true)
     */
    protected $applicationReceiptDate;

    /**
     * Outcome sent date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="outcome_sent_date", nullable=true)
     */
    protected $outcomeSentDate;

    /**
     * Close date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="close_date", nullable=true)
     */
    protected $closeDate;

    /**
     * Pi venue other
     *
     * @var string
     *
     * @ORM\Column(type="string", name="pi_venue_other", length=255, nullable=true)
     */
    protected $piVenueOther;


    /**
     * Set the pi venue
     *
     * @param \Olcs\Db\Entity\PiVenue $piVenue
     * @return Impounding
     */
    public function setPiVenue($piVenue)
    {
        $this->piVenue = $piVenue;

        return $this;
    }

    /**
     * Get the pi venue
     *
     * @return \Olcs\Db\Entity\PiVenue
     */
    public function getPiVenue()
    {
        return $this->piVenue;
    }

    /**
     * Set the impounding type
     *
     * @param \Olcs\Db\Entity\RefData $impoundingType
     * @return Impounding
     */
    public function setImpoundingType($impoundingType)
    {
        $this->impoundingType = $impoundingType;

        return $this;
    }

    /**
     * Get the impounding type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getImpoundingType()
    {
        return $this->impoundingType;
    }

    /**
     * Set the application receipt date
     *
     * @param \DateTime $applicationReceiptDate
     * @return Impounding
     */
    public function setApplicationReceiptDate($applicationReceiptDate)
    {
        $this->applicationReceiptDate = $applicationReceiptDate;

        return $this;
    }

    /**
     * Get the application receipt date
     *
     * @return \DateTime
     */
    public function getApplicationReceiptDate()
    {
        return $this->applicationReceiptDate;
    }

    /**
     * Set the outcome sent date
     *
     * @param \DateTime $outcomeSentDate
     * @return Impounding
     */
    public function setOutcomeSentDate($outcomeSentDate)
    {
        $this->outcomeSentDate = $outcomeSentDate;

        return $this;
    }

    /**
     * Get the outcome sent date
     *
     * @return \DateTime
     */
    public function getOutcomeSentDate()
    {
        return $this->outcomeSentDate;
    }

    /**
     * Set the close date
     *
     * @param \DateTime $closeDate
     * @return Impounding
     */
    public function setCloseDate($closeDate)
    {
        $this->closeDate = $closeDate;

        return $this;
    }

    /**
     * Get the close date
     *
     * @return \DateTime
     */
    public function getCloseDate()
    {
        return $this->closeDate;
    }

    /**
     * Set the pi venue other
     *
     * @param string $piVenueOther
     * @return Impounding
     */
    public function setPiVenueOther($piVenueOther)
    {
        $this->piVenueOther = $piVenueOther;

        return $this;
    }

    /**
     * Get the pi venue other
     *
     * @return string
     */
    public function getPiVenueOther()
    {
        return $this->piVenueOther;
    }
}
