<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Is trailer field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait IsTrailerField
{
    /**
     * Is trailer
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="is_trailer", nullable=true)
     */
    protected $isTrailer;

    /**
     * Set the is trailer
     *
     * @param string $isTrailer
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setIsTrailer($isTrailer)
    {
        $this->isTrailer = $isTrailer;

        return $this;
    }

    /**
     * Get the is trailer
     *
     * @return string
     */
    public function getIsTrailer()
    {
        return $this->isTrailer;
    }

}
