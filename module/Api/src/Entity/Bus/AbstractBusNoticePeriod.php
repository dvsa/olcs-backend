<?php

namespace Dvsa\Olcs\Api\Entity\Bus;

use Doctrine\ORM\Mapping as ORM;

/**
 * BusNoticePeriod Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="bus_notice_period",
 *    indexes={
 *        @ORM\Index(name="ix_bus_notice_period_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_bus_notice_period_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
abstract class AbstractBusNoticePeriod
{

    /**
     * Cancellation period
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="cancellation_period", nullable=false)
     */
    protected $cancellationPeriod;

    /**
     * Created by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     */
    protected $createdBy;

    /**
     * Created on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_on", nullable=true)
     */
    protected $createdOn;

    /**
     * Identifier - Id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * Last modified by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
     * @ORM\JoinColumn(name="last_modified_by", referencedColumnName="id", nullable=true)
     */
    protected $lastModifiedBy;

    /**
     * Last modified on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="last_modified_on", nullable=true)
     */
    protected $lastModifiedOn;

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
     * @ORM\Column(type="smallint", name="standard_period", nullable=false)
     */
    protected $standardPeriod;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="version", nullable=false, options={"default": 1})
     * @ORM\Version
     */
    protected $version = 1;

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

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy
     * @return BusNoticePeriod
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get the created by
     *
     * @return \Dvsa\Olcs\Api\Entity\User\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set the created on
     *
     * @param \DateTime $createdOn
     * @return BusNoticePeriod
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get the created on
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set the id
     *
     * @param int $id
     * @return BusNoticePeriod
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy
     * @return BusNoticePeriod
     */
    public function setLastModifiedBy($lastModifiedBy)
    {
        $this->lastModifiedBy = $lastModifiedBy;

        return $this;
    }

    /**
     * Get the last modified by
     *
     * @return \Dvsa\Olcs\Api\Entity\User\User
     */
    public function getLastModifiedBy()
    {
        return $this->lastModifiedBy;
    }

    /**
     * Set the last modified on
     *
     * @param \DateTime $lastModifiedOn
     * @return BusNoticePeriod
     */
    public function setLastModifiedOn($lastModifiedOn)
    {
        $this->lastModifiedOn = $lastModifiedOn;

        return $this;
    }

    /**
     * Get the last modified on
     *
     * @return \DateTime
     */
    public function getLastModifiedOn()
    {
        return $this->lastModifiedOn;
    }

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
     * Set the version
     *
     * @param int $version
     * @return BusNoticePeriod
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get the version
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set the createdOn field on persist
     *
     * @ORM\PrePersist
     */
    public function setCreatedOnBeforePersist()
    {
        $this->createdOn = new \DateTime();
    }

    /**
     * Set the lastModifiedOn field on persist
     *
     * @ORM\PreUpdate
     */
    public function setLastModifiedOnBeforeUpdate()
    {
        $this->lastModifiedOn = new \DateTime();
    }

    /**
     * Clear properties
     *
     * @param type $properties
     */
    public function clearProperties($properties = array())
    {
        foreach ($properties as $property) {

            if (property_exists($this, $property)) {
                if ($this->$property instanceof Collection) {

                    $this->$property = new ArrayCollection(array());

                } else {

                    $this->$property = null;
                }
            }
        }
    }
}
