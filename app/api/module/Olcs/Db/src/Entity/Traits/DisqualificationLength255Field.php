<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Disqualification length255 field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait DisqualificationLength255Field
{
    /**
     * Disqualification length
     *
     * @var string
     *
     * @ORM\Column(type="string", name="disqualification_length", length=255, nullable=true)
     */
    protected $disqualificationLength;

    /**
     * Set the disqualification length
     *
     * @param string $disqualificationLength
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setDisqualificationLength($disqualificationLength)
    {
        $this->disqualificationLength = $disqualificationLength;

        return $this;
    }

    /**
     * Get the disqualification length
     *
     * @return string
     */
    public function getDisqualificationLength()
    {
        return $this->disqualificationLength;
    }
}
