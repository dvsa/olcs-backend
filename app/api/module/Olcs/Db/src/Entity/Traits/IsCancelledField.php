<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Is cancelled field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait IsCancelledField
{
    /**
     * Is cancelled
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_cancelled", nullable=false)
     */
    protected $isCancelled = 0;

    /**
     * Set the is cancelled
     *
     * @param string $isCancelled
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setIsCancelled($isCancelled)
    {
        $this->isCancelled = $isCancelled;

        return $this;
    }

    /**
     * Get the is cancelled
     *
     * @return string
     */
    public function getIsCancelled()
    {
        return $this->isCancelled;
    }
}
