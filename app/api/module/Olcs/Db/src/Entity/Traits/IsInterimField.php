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
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="is_interim", nullable=false)
     */
    protected $isInterim = 0;

    /**
     * Set the is interim
     *
     * @param unknown $isInterim
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
     * @return unknown
     */
    public function getIsInterim()
    {
        return $this->isInterim;
    }

}
