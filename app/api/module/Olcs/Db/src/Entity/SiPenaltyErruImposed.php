<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * SiPenaltyErruImposed Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="si_penalty_erru_imposed",
 *    indexes={
 *        @ORM\Index(name="fk_si_penalty_erru_mposed_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_si_penalty_erru_mposed_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_si_penalty_erru_mposed_serious_infringement1_idx", columns={"serious_infringement_id"}),
 *        @ORM\Index(name="fk_si_penalty_erru_mposed_si_penalty_imposed_type1_idx", columns={"si_penalty_imposed_type_id"})
 *    }
 * )
 */
class SiPenaltyErruImposed implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\SeriousInfringementManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\StartDateFieldAlt1,
        Traits\EndDateFieldAlt1,
        Traits\CustomDeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Si penalty imposed type
     *
     * @var \Olcs\Db\Entity\SiPenaltyImposedType
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\SiPenaltyImposedType", fetch="LAZY")
     * @ORM\JoinColumn(name="si_penalty_imposed_type_id", referencedColumnName="id", nullable=false)
     */
    protected $siPenaltyImposedType;

    /**
     * Final decision date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="final_decision_date", nullable=true)
     */
    protected $finalDecisionDate;

    /**
     * Executed
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="executed", nullable=true)
     */
    protected $executed;


    /**
     * Set the si penalty imposed type
     *
     * @param \Olcs\Db\Entity\SiPenaltyImposedType $siPenaltyImposedType
     * @return SiPenaltyErruImposed
     */
    public function setSiPenaltyImposedType($siPenaltyImposedType)
    {
        $this->siPenaltyImposedType = $siPenaltyImposedType;

        return $this;
    }

    /**
     * Get the si penalty imposed type
     *
     * @return \Olcs\Db\Entity\SiPenaltyImposedType
     */
    public function getSiPenaltyImposedType()
    {
        return $this->siPenaltyImposedType;
    }

    /**
     * Set the final decision date
     *
     * @param \DateTime $finalDecisionDate
     * @return SiPenaltyErruImposed
     */
    public function setFinalDecisionDate($finalDecisionDate)
    {
        $this->finalDecisionDate = $finalDecisionDate;

        return $this;
    }

    /**
     * Get the final decision date
     *
     * @return \DateTime
     */
    public function getFinalDecisionDate()
    {
        return $this->finalDecisionDate;
    }

    /**
     * Set the executed
     *
     * @param int $executed
     * @return SiPenaltyErruImposed
     */
    public function setExecuted($executed)
    {
        $this->executed = $executed;

        return $this;
    }

    /**
     * Get the executed
     *
     * @return int
     */
    public function getExecuted()
    {
        return $this->executed;
    }
}
