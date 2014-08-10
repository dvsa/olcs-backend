<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Is decision field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait IsDecisionField
{
    /**
     * Is decision
     *
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="is_decision", nullable=false)
     */
    protected $isDecision;

    /**
     * Set the is decision
     *
     * @param unknown $isDecision
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setIsDecision($isDecision)
    {
        $this->isDecision = $isDecision;

        return $this;
    }

    /**
     * Get the is decision
     *
     * @return unknown
     */
    public function getIsDecision()
    {
        return $this->isDecision;
    }

}
