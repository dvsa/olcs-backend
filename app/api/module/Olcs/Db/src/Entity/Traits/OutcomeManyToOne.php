<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Outcome many to one trait
 *
 * Auto-Generated (Shared between 3 entities)
 */
trait OutcomeManyToOne
{
    /**
     * Outcome
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="outcome", referencedColumnName="id")
     */
    protected $outcome;

    /**
     * Set the outcome
     *
     * @param \Olcs\Db\Entity\RefData $outcome
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setOutcome($outcome)
    {
        $this->outcome = $outcome;

        return $this;
    }

    /**
     * Get the outcome
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getOutcome()
    {
        return $this->outcome;
    }
}
