<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * No of trailers required field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait NoOfTrailersRequiredField
{
    /**
     * No of trailers required
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="no_of_trailers_required", nullable=true)
     */
    protected $noOfTrailersRequired;

    /**
     * Set the no of trailers required
     *
     * @param int $noOfTrailersRequired
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setNoOfTrailersRequired($noOfTrailersRequired)
    {
        $this->noOfTrailersRequired = $noOfTrailersRequired;

        return $this;
    }

    /**
     * Get the no of trailers required
     *
     * @return int
     */
    public function getNoOfTrailersRequired()
    {
        return $this->noOfTrailersRequired;
    }
}
