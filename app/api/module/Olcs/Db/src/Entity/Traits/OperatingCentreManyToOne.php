<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Operating centre many to one trait
 *
 * Auto-Generated (Shared between 7 entities)
 */
trait OperatingCentreManyToOne
{
    /**
     * Operating centre
     *
     * @var \Olcs\Db\Entity\OperatingCentre
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\OperatingCentre")
     * @ORM\JoinColumn(name="operating_centre_id", referencedColumnName="id", nullable=false)
     */
    protected $operatingCentre;

    /**
     * Set the operating centre
     *
     * @param \Olcs\Db\Entity\OperatingCentre $operatingCentre
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setOperatingCentre($operatingCentre)
    {
        $this->operatingCentre = $operatingCentre;

        return $this;
    }

    /**
     * Get the operating centre
     *
     * @return \Olcs\Db\Entity\OperatingCentre
     */
    public function getOperatingCentre()
    {
        return $this->operatingCentre;
    }
}
