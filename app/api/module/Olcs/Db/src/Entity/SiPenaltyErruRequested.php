<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * SiPenaltyErruRequested Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="si_penalty_erru_requested",
 *    indexes={
 *        @ORM\Index(name="fk_si_penalty_erru_requested_serious_infringement1_idx", columns={"serious_infringement_id"}),
 *        @ORM\Index(name="fk_si_penalty_erru_requested_si_penalty_requested_type1_idx", columns={"si_penalty_requested_type_id"}),
 *        @ORM\Index(name="fk_si_penalty_erru_requested_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_si_penalty_erru_requested_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class SiPenaltyErruRequested implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Duration
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="duration", nullable=true)
     */
    protected $duration;

    /**
     * Serious infringement
     *
     * @var \Olcs\Db\Entity\SeriousInfringement
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\SeriousInfringement", inversedBy="requestedErrus")
     * @ORM\JoinColumn(name="serious_infringement_id", referencedColumnName="id", nullable=false)
     */
    protected $seriousInfringement;

    /**
     * Si penalty requested type
     *
     * @var \Olcs\Db\Entity\SiPenaltyRequestedType
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\SiPenaltyRequestedType")
     * @ORM\JoinColumn(name="si_penalty_requested_type_id", referencedColumnName="id", nullable=false)
     */
    protected $siPenaltyRequestedType;

    /**
     * Set the duration
     *
     * @param int $duration
     * @return SiPenaltyErruRequested
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get the duration
     *
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set the serious infringement
     *
     * @param \Olcs\Db\Entity\SeriousInfringement $seriousInfringement
     * @return SiPenaltyErruRequested
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
     * Set the si penalty requested type
     *
     * @param \Olcs\Db\Entity\SiPenaltyRequestedType $siPenaltyRequestedType
     * @return SiPenaltyErruRequested
     */
    public function setSiPenaltyRequestedType($siPenaltyRequestedType)
    {
        $this->siPenaltyRequestedType = $siPenaltyRequestedType;

        return $this;
    }

    /**
     * Get the si penalty requested type
     *
     * @return \Olcs\Db\Entity\SiPenaltyRequestedType
     */
    public function getSiPenaltyRequestedType()
    {
        return $this->siPenaltyRequestedType;
    }
}
