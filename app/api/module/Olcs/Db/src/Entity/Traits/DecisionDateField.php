<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Decision date field trait
 *
 * Auto-Generated (Shared between 3 entities)
 */
trait DecisionDateField
{
    /**
     * Decision date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="decision_date", nullable=true)
     */
    protected $decisionDate;

    /**
     * Set the decision date
     *
     * @param \DateTime $decisionDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setDecisionDate($decisionDate)
    {
        $this->decisionDate = $decisionDate;

        return $this;
    }

    /**
     * Get the decision date
     *
     * @return \DateTime
     */
    public function getDecisionDate()
    {
        return $this->decisionDate;
    }
}
