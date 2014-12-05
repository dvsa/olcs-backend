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
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\EndDateField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\StartDateFieldAlt1,
        Traits\CustomVersionField;

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
     * Serious infringement
     *
     * @var \Olcs\Db\Entity\SeriousInfringement
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\SeriousInfringement", inversedBy="appliedPenalties")
     * @ORM\JoinColumn(name="serious_infringement_id", referencedColumnName="id", nullable=false)
     */
    protected $seriousInfringement;

    /**
     * Si penalty type
     *
     * @var \Olcs\Db\Entity\SiPenaltyType
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\SiPenaltyType")
     * @ORM\JoinColumn(name="si_penalty_type_id", referencedColumnName="id", nullable=false)
     */
    protected $siPenaltyType;

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

    /**
     * Set the serious infringement
     *
     * @param \Olcs\Db\Entity\SeriousInfringement $seriousInfringement
     * @return SiPenalty
     */
    public function setSeriousInfringement($seriousInfringement)
    {
        $this->seriousInfringement = $seriousInfringement;

        return $this;
    }

    /**
     * Get the serious infringement
     *
     * @return \Olcs\Db\Entity\SeriousInfringement
     */
    public function getSeriousInfringement()
    {
        return $this->seriousInfringement;
    }

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
}
