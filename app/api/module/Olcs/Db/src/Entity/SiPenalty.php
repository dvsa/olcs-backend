<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * SiPenalty Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="si_penalty",
 *    indexes={
 *        @ORM\Index(name="IDX_B0BCD0DE749FD19F", columns={"si_penalty_type_id"}),
 *        @ORM\Index(name="IDX_B0BCD0DEDE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_B0BCD0DEDC3970F4", columns={"serious_infringement_id"}),
 *        @ORM\Index(name="IDX_B0BCD0DE65CF370E", columns={"last_modified_by"})
 *    }
 * )
 */
class SiPenalty implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\SeriousInfringementManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\StartDateFieldAlt1,
        Traits\EndDateField,
        Traits\CustomDeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Si penalty type
     *
     * @var \Olcs\Db\Entity\SiPenaltyType
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\SiPenaltyType", fetch="LAZY")
     * @ORM\JoinColumn(name="si_penalty_type_id", referencedColumnName="id", nullable=false)
     */
    protected $siPenaltyType;

    /**
     * Imposed
     *
     * @var string
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
     * @return SiPenalty
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
     * @param string $imposed
     * @return SiPenalty
     */
    public function setImposed($imposed)
    {
        $this->imposed = $imposed;

        return $this;
    }

    /**
     * Get the imposed
     *
     * @return string
     */
    public function getImposed()
    {
        return $this->imposed;
    }

    /**
     * Set the reason not imposed
     *
     * @param string $reasonNotImposed
     * @return SiPenalty
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
