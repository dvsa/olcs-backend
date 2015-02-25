<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Hearing Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="hearing",
 *    indexes={
 *        @ORM\Index(name="fk_hearing_hearing_type_idx", columns={"hearing_type"}),
 *        @ORM\Index(name="fk_hearing_cases1_idx", columns={"case_id"}),
 *        @ORM\Index(name="fk_hearing_pi_venue1_idx", columns={"venue_id"}),
 *        @ORM\Index(name="fk_hearing_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_hearing_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_hearing_presiding_tc1_idx", columns={"presiding_tc_id"})
 *    }
 * )
 */
class Hearing implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CaseManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\HearingDateField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\PresidingTcManyToOne,
        Traits\CustomVersionField;

    /**
     * Agreed by tc date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="agreed_by_tc_date", nullable=true)
     */
    protected $agreedByTcDate;

    /**
     * Hearing type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="hearing_type", referencedColumnName="id", nullable=false)
     */
    protected $hearingType;

    /**
     * Venue
     *
     * @var \Olcs\Db\Entity\PiVenue
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\PiVenue")
     * @ORM\JoinColumn(name="venue_id", referencedColumnName="id", nullable=true)
     */
    protected $venue;

    /**
     * Venue other
     *
     * @var string
     *
     * @ORM\Column(type="string", name="venue_other", length=255, nullable=true)
     */
    protected $venueOther;

    /**
     * Witness count
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="witness_count", nullable=false, options={"default": 0})
     */
    protected $witnessCount = 0;

    /**
     * Set the agreed by tc date
     *
     * @param \DateTime $agreedByTcDate
     * @return Hearing
     */
    public function setAgreedByTcDate($agreedByTcDate)
    {
        $this->agreedByTcDate = $agreedByTcDate;

        return $this;
    }

    /**
     * Get the agreed by tc date
     *
     * @return \DateTime
     */
    public function getAgreedByTcDate()
    {
        return $this->agreedByTcDate;
    }

    /**
     * Set the hearing type
     *
     * @param \Olcs\Db\Entity\RefData $hearingType
     * @return Hearing
     */
    public function setHearingType($hearingType)
    {
        $this->hearingType = $hearingType;

        return $this;
    }

    /**
     * Get the hearing type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getHearingType()
    {
        return $this->hearingType;
    }

    /**
     * Set the venue
     *
     * @param \Olcs\Db\Entity\PiVenue $venue
     * @return Hearing
     */
    public function setVenue($venue)
    {
        $this->venue = $venue;

        return $this;
    }

    /**
     * Get the venue
     *
     * @return \Olcs\Db\Entity\PiVenue
     */
    public function getVenue()
    {
        return $this->venue;
    }

    /**
     * Set the venue other
     *
     * @param string $venueOther
     * @return Hearing
     */
    public function setVenueOther($venueOther)
    {
        $this->venueOther = $venueOther;

        return $this;
    }

    /**
     * Get the venue other
     *
     * @return string
     */
    public function getVenueOther()
    {
        return $this->venueOther;
    }

    /**
     * Set the witness count
     *
     * @param int $witnessCount
     * @return Hearing
     */
    public function setWitnessCount($witnessCount)
    {
        $this->witnessCount = $witnessCount;

        return $this;
    }

    /**
     * Get the witness count
     *
     * @return int
     */
    public function getWitnessCount()
    {
        return $this->witnessCount;
    }
}
