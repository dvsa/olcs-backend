<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Is printing field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait IsPrintingField
{
    /**
     * Is printing
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_printing", nullable=false, options={"default": 0})
     */
    protected $isPrinting;

    /**
     * Set the is printing
     *
     * @param string $isPrinting
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setIsPrinting($isPrinting)
    {
        $this->isPrinting = $isPrinting;

        return $this;
    }

    /**
     * Get the is printing
     *
     * @return string
     */
    public function getIsPrinting()
    {
        return $this->isPrinting;
    }
}
