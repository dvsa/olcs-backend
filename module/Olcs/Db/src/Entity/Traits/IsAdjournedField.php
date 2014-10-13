<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Is adjourned field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait IsAdjournedField
{
    /**
     * Is adjourned
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_adjourned", nullable=false)
     */
    protected $isAdjourned = 0;

    /**
     * Set the is adjourned
     *
     * @param string $isAdjourned
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setIsAdjourned($isAdjourned)
    {
        $this->isAdjourned = $isAdjourned;

        return $this;
    }

    /**
     * Get the is adjourned
     *
     * @return string
     */
    public function getIsAdjourned()
    {
        return $this->isAdjourned;
    }
}
