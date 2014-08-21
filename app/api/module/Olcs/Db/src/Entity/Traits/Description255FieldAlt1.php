<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description255 field alt1 trait
 *
 * Auto-Generated (Shared between 8 entities)
 */
trait Description255FieldAlt1
{
    /**
     * Description
     *
     * @var string
     *
     * @ORM\Column(type="string", name="description", length=255, nullable=false)
     */
    protected $description;

    /**
     * Set the description
     *
     * @param string $description
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
