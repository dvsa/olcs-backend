<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pub type3 field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait PubType3Field
{
    /**
     * Pub type
     *
     * @var string
     *
     * @ORM\Column(type="string", name="pub_type", length=3, nullable=false)
     */
    protected $pubType;

    /**
     * Set the pub type
     *
     * @param string $pubType
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setPubType($pubType)
    {
        $this->pubType = $pubType;

        return $this;
    }

    /**
     * Get the pub type
     *
     * @return string
     */
    public function getPubType()
    {
        return $this->pubType;
    }

}
