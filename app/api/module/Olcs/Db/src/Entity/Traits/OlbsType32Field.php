<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Olbs type32 field trait
 *
 * Auto-Generated (Shared between 15 entities)
 */
trait OlbsType32Field
{
    /**
     * Olbs type
     *
     * @var string
     *
     * @ORM\Column(type="string", name="olbs_type", length=32, nullable=true)
     */
    protected $olbsType;

    /**
     * Set the olbs type
     *
     * @param string $olbsType
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setOlbsType($olbsType)
    {
        $this->olbsType = $olbsType;

        return $this;
    }

    /**
     * Get the olbs type
     *
     * @return string
     */
    public function getOlbsType()
    {
        return $this->olbsType;
    }
}
