<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * SiPenalty Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="si_penalty",
 *    indexes={
 *        @ORM\Index(name="fk_si_penalty_serious_infringement1_idx", columns={"serious_infringement_id"}),
 *        @ORM\Index(name="fk_si_penalty_si_penalty_type1_idx", columns={"si_penalty_type_id"}),
 *        @ORM\Index(name="fk_si_penalty_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_si_penalty_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class SiPenalty implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\SeriousInfringementManyToOne,
        Traits\StartDateFieldAlt1,
        Traits\EndDateField,
        Traits\DeletedDateFieldAlt1,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Si penalty type
     *
     * @var \Olcs\Db\Entity\SiPenaltyType
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\SiPenaltyType")
     * @ORM\JoinColumn(name="si_penalty_type_id", referencedColumnName="id")
     */
    protected $siPenaltyType;

    /**
     * Imposed
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="imposed", nullable=true)
     */
    protected $imposed;

    /**
     * Reason not imposed
     *
     * @var string
     *
     * @ORM\Column(type="string", name="reason_not_imposed", length=500, nullable=true)
     */
    protected $reasonNotImposed;

    /**
     * Set the si penalty type
     *
     * @param \Olcs\Db\Entity\SiPenaltyType $siPenaltyType
     * @return \Olcs\Db\Entity\SiPenalty
     */
    public function setSiPenaltyType($siPenaltyType)
    {
        $this->siPenaltyType = $siPenaltyType;

        return $this;
    }

    /**
     * Get the si penalty type
     *
     * @return \Olcs\Db\Entity\SiPenaltyType
     */
    public function getSiPenaltyType()
    {
        return $this->siPenaltyType;
    }

    /**
     * Set the imposed
     *
     * @param boolean $imposed
     * @return \Olcs\Db\Entity\SiPenalty
     */
    public function setImposed($imposed)
    {
        $this->imposed = $imposed;

        return $this;
    }

    /**
     * Get the imposed
     *
     * @return boolean
     */
    public function getImposed()
    {
        return $this->imposed;
    }

    /**
     * Set the reason not imposed
     *
     * @param string $reasonNotImposed
     * @return \Olcs\Db\Entity\SiPenalty
     */
    public function setReasonNotImposed($reasonNotImposed)
    {
        $this->reasonNotImposed = $reasonNotImposed;

        return $this;
    }

    /**
     * Get the reason not imposed
     *
     * @return string
     */
    public function getReasonNotImposed()
    {
        return $this->reasonNotImposed;
    }
}
