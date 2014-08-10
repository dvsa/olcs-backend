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
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\SeriousInfringementManyToOne,
        Traits\CustomDeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Si penalty requested type
     *
     * @var \Olcs\Db\Entity\SiPenaltyRequestedType
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\SiPenaltyRequestedType")
     * @ORM\JoinColumn(name="si_penalty_requested_type_id", referencedColumnName="id")
     */
    protected $siPenaltyRequestedType;

    /**
     * Duration
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="duration", nullable=true)
     */
    protected $duration;

    /**
     * Get identifier(s)
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->getId();
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

}
