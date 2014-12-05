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
     * Executed
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="executed", nullable=true)
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
     * @param boolean $executed
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
     * @return boolean
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
