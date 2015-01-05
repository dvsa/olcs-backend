<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Olbs key field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait OlbsKeyField
{
    /**
     * Olbs key
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="olbs_key", nullable=true)
     */
    protected $olbsKey;

    /**
     * Set the olbs key
     *
     * @param int $olbsKey
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setOlbsKey($olbsKey)
    {
        $this->olbsKey = $olbsKey;

        return $this;
    }

    /**
     * Get the olbs key
     *
     * @return int
     */
    public function getOlbsKey()
    {
        return $this->olbsKey;
    }
}
