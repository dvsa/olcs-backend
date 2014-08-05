<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Is irfo field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait IsIrfoField
{
    /**
     * Is irfo
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="is_irfo", nullable=false)
     */
    protected $isIrfo = 0;

    /**
     * Set the is irfo
     *
     * @param boolean $isIrfo
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setIsIrfo($isIrfo)
    {
        $this->isIrfo = $isIrfo;

        return $this;
    }

    /**
     * Get the is irfo
     *
     * @return boolean
     */
    public function getIsIrfo()
    {
        return $this->isIrfo;
    }
}
