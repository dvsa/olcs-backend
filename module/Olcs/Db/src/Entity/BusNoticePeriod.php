<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * BusNoticePeriod Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="bus_notice_period",
 *    indexes={
 *        @ORM\Index(name="fk_bus_notice_period_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_bus_notice_period_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class BusNoticePeriod implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Notice area
     *
     * @var string
     *
     * @ORM\Column(type="string", name="notice_area", length=70, nullable=false)
     */
    protected $noticeArea;

    /**
     * Standard period
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="standard_period", nullable=false)
     */
    protected $standardPeriod;

    /**
     * Cancellation period
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="cancellation_period", nullable=false)
     */
    protected $cancellationPeriod;

    /**
     * Set the notice area
     *
     * @param string $noticeArea
     * @return BusNoticePeriod
     */
    public function setNoticeArea($noticeArea)
    {
        $this->noticeArea = $noticeArea;

        return $this;
    }

    /**
     * Get the notice area
     *
     * @return string
     */
    public function getNoticeArea()
    {
        return $this->noticeArea;
    }


    /**
     * Set the standard period
     *
     * @param int $standardPeriod
     * @return BusNoticePeriod
     */
    public function setStandardPeriod($standardPeriod)
    {
        $this->standardPeriod = $standardPeriod;

        return $this;
    }

    /**
     * Get the standard period
     *
     * @return int
     */
    public function getStandardPeriod()
    {
        return $this->standardPeriod;
    }


    /**
     * Set the cancellation period
     *
     * @param int $cancellationPeriod
     * @return BusNoticePeriod
     */
    public function setCancellationPeriod($cancellationPeriod)
    {
        $this->cancellationPeriod = $cancellationPeriod;

        return $this;
    }

    /**
     * Get the cancellation period
     *
     * @return int
     */
    public function getCancellationPeriod()
    {
        return $this->cancellationPeriod;
    }

}
