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
 *        @ORM\Index(name="IDX_1BEFF80BDE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_1BEFF80B9E6B1585", columns={"organisation_id"}),
 *        @ORM\Index(name="IDX_1BEFF80B65CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_1BEFF80B217BBB47", columns={"person_id"}),
 *        @ORM\Index(name="IDX_1BEFF80B1F75BD29", columns={"transport_manager_id"})
 *    }
 * )
 */
class Disqualification implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\OrganisationManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\PersonManyToOne,
        Traits\TransportManagerManyToOne,
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
