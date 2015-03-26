<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Title many to one trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait TitleManyToOne
{
    /**
     * Title
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="title", referencedColumnName="id", nullable=true)
     */
    protected $title;

    /**
     * Set the title
     *
     * @param \Olcs\Db\Entity\RefData $title
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
     * @return \Olcs\Db\Entity\RefData
     */
    public function getTitle()
    {
        return $this->title;
    }
}
