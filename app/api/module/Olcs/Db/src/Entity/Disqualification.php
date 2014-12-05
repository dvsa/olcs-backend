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
 *        @ORM\Index(name="fk_disqualification_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_disqualification_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class Disqualification implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\Notes4000Field,
        Traits\OrganisationManyToOne,
        Traits\StartDateFieldAlt1,
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
     * Period
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="period", nullable=false)
     */
    protected $period;

    /**
     * Person
     *
     * @var \Olcs\Db\Entity\Person
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Person")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id", nullable=true)
     */
    protected $person;

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

    /**
     * Set the person
     *
     * @param \Olcs\Db\Entity\Person $person
     * @return Disqualification
     */
    public function setPerson($person)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get the person
     *
     * @return \Olcs\Db\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }
}
