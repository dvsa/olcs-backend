<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tm case decision many to one trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait TmCaseDecisionManyToOne
{
    /**
     * Tm case decision
     *
     * @var \Olcs\Db\Entity\TmCaseDecision
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TmCaseDecision", fetch="LAZY")
     * @ORM\JoinColumn(name="tm_case_decision_id", referencedColumnName="id", nullable=false)
     */
    protected $tmCaseDecision;

    /**
     * Set the tm case decision
     *
     * @param \Olcs\Db\Entity\TmCaseDecision $tmCaseDecision
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setTmCaseDecision($tmCaseDecision)
    {
        $this->tmCaseDecision = $tmCaseDecision;

        return $this;
    }

    /**
     * Get the tm case decision
     *
     * @return \Olcs\Db\Entity\TmCaseDecision
     */
    public function getTmCaseDecision()
    {
        return $this->tmCaseDecision;
    }

}
