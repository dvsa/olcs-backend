<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tm type many to one trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait TmTypeManyToOne
{
    /**
     * Tm type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="tm_type", referencedColumnName="id", nullable=true)
     */
    protected $tmType;

    /**
     * Set the tm type
     *
     * @param \Olcs\Db\Entity\RefData $tmType
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setTmType($tmType)
    {
        $this->tmType = $tmType;

        return $this;
    }

    /**
     * Get the tm type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getTmType()
    {
        return $this->tmType;
    }
}
