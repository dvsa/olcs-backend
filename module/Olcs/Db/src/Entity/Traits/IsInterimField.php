<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Is interim field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait IsInterimField
{
    /**
     * Is interim
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="is_interim", nullable=false)
     */
    protected $isInterim = 0;

    /**
     * Set the is interim
     *
     * @param boolean $isInterim
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setIsInterim($isInterim)
    {
        $this->isInterim = $isInterim;

        return $this;
    }

    /**
     * Get the is interim
     *
     * @return boolean
     */
    public function getIsInterim()
    {
        return $this->isInterim;
    }
}
