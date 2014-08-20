<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Publication no field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait PublicationNoField
{
    /**
     * Publication no
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="publication_no", nullable=false)
     */
    protected $publicationNo;

    /**
     * Set the publication no
     *
     * @param int $publicationNo
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setPublicationNo($publicationNo)
    {
        $this->publicationNo = $publicationNo;

        return $this;
    }

    /**
     * Get the publication no
     *
     * @return int
     */
    public function getPublicationNo()
    {
        return $this->publicationNo;
    }
}
