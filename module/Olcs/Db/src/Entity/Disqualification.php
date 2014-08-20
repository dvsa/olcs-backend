<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Disqualification Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="disqualification",
 *    indexes={
 *        @ORM\Index(name="fk_disqualification_person1_idx", columns={"person_id"}),
 *        @ORM\Index(name="fk_disqualification_organisation1_idx", columns={"organisation_id"}),
 *        @ORM\Index(name="fk_disqualification_transport_manager1_idx", columns={"transport_manager_id"}),
 *        @ORM\Index(name="fk_disqualification_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_disqualification_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class Disqualification implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\TransportManagerManyToOne,
        Traits\OrganisationManyToOne,
        Traits\PersonManyToOne,
        Traits\StartDateFieldAlt1,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Is disqualified
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="is_disqualified", nullable=true)
     */
    protected $isDisqualified;

    /**
     * Notes
     *
     * @var string
     *
     * @ORM\Column(type="string", name="notes", length=45, nullable=true)
     */
    protected $notes;

    /**
     * Period
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="period", nullable=false)
     */
    protected $period;


    /**
     * Set the is disqualified
     *
     * @param string $isDisqualified
     * @return Disqualification
     */
    public function setIsDisqualified($isDisqualified)
    {
        $this->isDisqualified = $isDisqualified;

        return $this;
    }

    /**
     * Get the is disqualified
     *
     * @return string
     */
    public function getIsDisqualified()
    {
        return $this->isDisqualified;
    }

    /**
     * Set the notes
     *
     * @param string $notes
     * @return Disqualification
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get the notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set the period
     *
     * @param int $period
     * @return Disqualification
     */
    public function setPeriod($period)
    {
        $this->period = $period;

        return $this;
    }

    /**
     * Get the period
     *
     * @return int
     */
    public function getPeriod()
    {
        return $this->period;
    }
}
