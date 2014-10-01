<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Is representation field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait IsRepresentationField
{
    /**
     * Is representation
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_representation", nullable=false)
     */
    protected $isRepresentation;

    /**
     * Set the is representation
     *
     * @param string $isRepresentation
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setIsRepresentation($isRepresentation)
    {
        $this->isRepresentation = $isRepresentation;

        return $this;
    }

    /**
     * Get the is representation
     *
     * @return string
     */
    public function getIsRepresentation()
    {
        return $this->isRepresentation;
    }
}
