<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * No of trailers possessed field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait NoOfTrailersPossessedField
{
    /**
     * No of trailers possessed
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="no_of_trailers_possessed", nullable=true)
     */
    protected $noOfTrailersPossessed;

    /**
     * Set the no of trailers possessed
     *
     * @param int $noOfTrailersPossessed
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setNoOfTrailersPossessed($noOfTrailersPossessed)
    {
        $this->noOfTrailersPossessed = $noOfTrailersPossessed;

        return $this;
    }

    /**
     * Get the no of trailers possessed
     *
     * @return int
     */
    public function getNoOfTrailersPossessed()
    {
        return $this->noOfTrailersPossessed;
    }
}
