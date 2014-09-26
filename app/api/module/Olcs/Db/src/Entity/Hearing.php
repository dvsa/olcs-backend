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
 *        @ORM\Index(name="IDX_77C63782DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_77C63782CF10D4F5", columns={"case_id"}),
 *        @ORM\Index(name="IDX_77C6378265CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_77C6378253BAD7A2", columns={"presiding_tc_id"}),
 *        @ORM\Index(name="IDX_77C6378240A73EBA", columns={"venue_id"})
 *    }
 * )
 */
class Hearing implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\CaseManyToOneAlt1,
        Traits\LastModifiedByManyToOne,
        Traits\PresidingTcManyToOne,
        Traits\VenueManyToOne,
        Traits\HearingDateField,
        Traits\CustomDeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
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
     * Witness count
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="witness_count", nullable=false)
     */
    protected $witnessCount;

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
