<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Title32 field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait Title32Field
{
    /**
     * Title
     *
     * @var string
     *
     * @ORM\Column(type="string", name="title", length=32, nullable=true)
     */
    protected $title;

    /**
     * Set the title
     *
     * @param string $title
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

}
