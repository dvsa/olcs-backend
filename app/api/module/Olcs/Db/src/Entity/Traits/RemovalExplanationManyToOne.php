<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Removal explanation many to one trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait RemovalExplanationManyToOne
{
    /**
     * Removal explanation
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="removal_explanation", referencedColumnName="id")
     */
    protected $removalExplanation;

    /**
     * Set the removal explanation
     *
     * @param \Olcs\Db\Entity\RefData $removalExplanation
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setRemovalExplanation($removalExplanation)
    {
        $this->removalExplanation = $removalExplanation;

        return $this;
    }

    /**
     * Get the removal explanation
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getRemovalExplanation()
    {
        return $this->removalExplanation;
    }

}
