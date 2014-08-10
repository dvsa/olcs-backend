<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Opposition many to one trait
 *
 * Auto-Generated (Shared between 3 entities)
 */
trait OppositionManyToOne
{
    /**
     * Opposition
     *
     * @var \Olcs\Db\Entity\Opposition
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Opposition")
     * @ORM\JoinColumn(name="opposition_id", referencedColumnName="id")
     */
    protected $opposition;

    /**
     * Set the opposition
     *
     * @param \Olcs\Db\Entity\Opposition $opposition
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setOpposition($opposition)
    {
        $this->opposition = $opposition;

        return $this;
    }

    /**
     * Get the opposition
     *
     * @return \Olcs\Db\Entity\Opposition
     */
    public function getOpposition()
    {
        return $this->opposition;
    }

}
