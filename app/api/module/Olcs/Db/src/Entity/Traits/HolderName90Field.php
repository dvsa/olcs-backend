<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Holder name90 field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait HolderName90Field
{
    /**
     * Holder name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="holder_name", length=90, nullable=true)
     */
    protected $holderName;

    /**
     * Set the holder name
     *
     * @param string $holderName
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setHolderName($holderName)
    {
        $this->holderName = $holderName;

        return $this;
    }

    /**
     * Get the holder name
     *
     * @return string
     */
    public function getHolderName()
    {
        return $this->holderName;
    }
}
