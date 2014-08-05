<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pi detail many to one trait
 *
 * Auto-Generated (Shared between 3 entities)
 */
trait PiDetailManyToOne
{
    /**
     * Pi detail
     *
     * @var \Olcs\Db\Entity\PiDetail
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\PiDetail")
     * @ORM\JoinColumn(name="pi_detail_id", referencedColumnName="id")
     */
    protected $piDetail;

    /**
     * Set the pi detail
     *
     * @param \Olcs\Db\Entity\PiDetail $piDetail
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setPiDetail($piDetail)
    {
        $this->piDetail = $piDetail;

        return $this;
    }

    /**
     * Get the pi detail
     *
     * @return \Olcs\Db\Entity\PiDetail
     */
    public function getPiDetail()
    {
        return $this->piDetail;
    }
}
