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
 *        @ORM\Index(name="ix_si_penalty_erru_imposed_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_si_penalty_erru_imposed_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_si_penalty_erru_imposed_serious_infringement_id", columns={"serious_infringement_id"}),
 *        @ORM\Index(name="ix_si_penalty_erru_imposed_si_penalty_imposed_type_id", columns={"si_penalty_imposed_type_id"}),
 *        @ORM\Index(name="ix_si_penalty_erru_imposed_executed", columns={"executed"})
 *    }
 * )
 */
class SiPenaltyErruImposed implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\EndDateField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\OlbsKeyField,
        Traits\StartDateFieldAlt1,
        Traits\CustomVersionField;

    /**
     * Executed
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="executed", referencedColumnName="id", nullable=true)
     */
    protected $executed;

    /**
     * Final decision date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="final_decision_date", nullable=true)
     */
    protected $finalDecisionDate;

    /**
     * Serious infringement
     *
     * @var \Olcs\Db\Entity\SeriousInfringement
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\SeriousInfringement", inversedBy="imposedErrus")
     * @ORM\JoinColumn(name="serious_infringement_id", referencedColumnName="id", nullable=false)
     */
    protected $seriousInfringement;

    /**
     * Si penalty imposed type
     *
     * @var \Olcs\Db\Entity\SiPenaltyImposedType
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\SiPenaltyImposedType")
     * @ORM\JoinColumn(name="si_penalty_imposed_type_id", referencedColumnName="id", nullable=false)
     */
    protected $siPenaltyImposedType;

    /**
     * Set the executed
     *
     * @param \Olcs\Db\Entity\RefData $executed
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
     * @return \Olcs\Db\Entity\RefData
     */
    public function getExecuted()
    {
        return $this->executed;
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
     * Set the serious infringement
     *
     * @param \Olcs\Db\Entity\SeriousInfringement $seriousInfringement
     * @return SiPenaltyErruImposed
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
}
