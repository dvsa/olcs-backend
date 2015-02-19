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
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_interim", nullable=false, options={"default": 0})
     */
    protected $isInterim;

    /**
     * Set the is interim
     *
     * @param string $isInterim
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
     * @return string
     */
    public function getIsInterim()
    {
        return $this->isInterim;
    }
}
