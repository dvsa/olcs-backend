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
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\VenueManyToOne,
        Traits\CaseManyToOne,
        Traits\HearingDateField,
        Traits\CustomDeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Presiding tc
     *
     * @var \Olcs\Db\Entity\PresidingTc
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\PresidingTc", fetch="LAZY")
     * @ORM\JoinColumn(name="presiding_tc_id", referencedColumnName="id")
     */
    protected $presidingTc = 0;

    /**
     * Agreed by tc date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="agreed_by_tc_date", nullable=true)
     */
    protected $agreedByTcDate;

    /**
     * Witness count
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="witness_count", nullable=false)
     */
    protected $witnessCount = 0;


    /**
     * Set the presiding tc
     *
     * @param \Olcs\Db\Entity\PresidingTc $presidingTc
     * @return Hearing
     */
    public function setPresidingTc($presidingTc)
    {
        $this->presidingTc = $presidingTc;

        return $this;
    }

    /**
     * Get the presiding tc
     *
     * @return \Olcs\Db\Entity\PresidingTc
     */
    public function getPresidingTc()
    {
        return $this->presidingTc;
    }

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
