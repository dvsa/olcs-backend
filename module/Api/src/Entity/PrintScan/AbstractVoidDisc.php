<?php

namespace Dvsa\Olcs\Api\Entity\PrintScan;

use Doctrine\ORM\Mapping as ORM;

/**
 * VoidDisc Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="void_disc",
 *    indexes={
 *        @ORM\Index(name="ix_void_disc_goods_or_psv", columns={"goods_or_psv"}),
 *        @ORM\Index(name="ix_void_disc_licence_type", columns={"licence_type"}),
 *        @ORM\Index(name="ix_void_disc_traffic_area_id", columns={"traffic_area_id"}),
 *        @ORM\Index(name="ix_void_disc_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_void_disc_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_void_disc_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
abstract class AbstractVoidDisc
{

    /**
     * Created by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User")
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
     * Goods or psv
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData")
     * @ORM\JoinColumn(name="goods_or_psv", referencedColumnName="id", nullable=false)
     */
    protected $goodsOrPsv;

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
     * Is ni self serve
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_ni_self_serve", nullable=false, options={"default": 0})
     */
    protected $isNiSelfServe = 0;

    /**
     * Is self serve
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_self_serve", nullable=false, options={"default": 0})
     */
    protected $isSelfServe = 0;

    /**
     * Last modified by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User")
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
     * Licence type
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData")
     * @ORM\JoinColumn(name="licence_type", referencedColumnName="id", nullable=false)
     */
    protected $licenceType;

    /**
     * Olbs key
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="olbs_key", nullable=true)
     */
    protected $olbsKey;

    /**
     * Serial end
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="serial_end", nullable=true)
     */
    protected $serialEnd;

    /**
     * Serial start
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="serial_start", nullable=true)
     */
    protected $serialStart;

    /**
     * Traffic area
     *
     * @var \Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea")
     * @ORM\JoinColumn(name="traffic_area_id", referencedColumnName="id", nullable=true)
     */
    protected $trafficArea;

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
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy
     * @return VoidDisc
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
     * @return VoidDisc
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
     * Set the goods or psv
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $goodsOrPsv
     * @return VoidDisc
     */
    public function setGoodsOrPsv($goodsOrPsv)
    {
        $this->goodsOrPsv = $goodsOrPsv;

        return $this;
    }

    /**
     * Get the goods or psv
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getGoodsOrPsv()
    {
        return $this->goodsOrPsv;
    }

    /**
     * Set the id
     *
     * @param int $id
     * @return VoidDisc
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
     * Set the is ni self serve
     *
     * @param boolean $isNiSelfServe
     * @return VoidDisc
     */
    public function setIsNiSelfServe($isNiSelfServe)
    {
        $this->isNiSelfServe = $isNiSelfServe;

        return $this;
    }

    /**
     * Get the is ni self serve
     *
     * @return boolean
     */
    public function getIsNiSelfServe()
    {
        return $this->isNiSelfServe;
    }

    /**
     * Set the is self serve
     *
     * @param boolean $isSelfServe
     * @return VoidDisc
     */
    public function setIsSelfServe($isSelfServe)
    {
        $this->isSelfServe = $isSelfServe;

        return $this;
    }

    /**
     * Get the is self serve
     *
     * @return boolean
     */
    public function getIsSelfServe()
    {
        return $this->isSelfServe;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy
     * @return VoidDisc
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
     * @return VoidDisc
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
     * Set the licence type
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $licenceType
     * @return VoidDisc
     */
    public function setLicenceType($licenceType)
    {
        $this->licenceType = $licenceType;

        return $this;
    }

    /**
     * Get the licence type
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getLicenceType()
    {
        return $this->licenceType;
    }

    /**
     * Set the olbs key
     *
     * @param int $olbsKey
     * @return VoidDisc
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

    /**
     * Set the serial end
     *
     * @param int $serialEnd
     * @return VoidDisc
     */
    public function setSerialEnd($serialEnd)
    {
        $this->serialEnd = $serialEnd;

        return $this;
    }

    /**
     * Get the serial end
     *
     * @return int
     */
    public function getSerialEnd()
    {
        return $this->serialEnd;
    }

    /**
     * Set the serial start
     *
     * @param int $serialStart
     * @return VoidDisc
     */
    public function setSerialStart($serialStart)
    {
        $this->serialStart = $serialStart;

        return $this;
    }

    /**
     * Get the serial start
     *
     * @return int
     */
    public function getSerialStart()
    {
        return $this->serialStart;
    }

    /**
     * Set the traffic area
     *
     * @param \Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea $trafficArea
     * @return VoidDisc
     */
    public function setTrafficArea($trafficArea)
    {
        $this->trafficArea = $trafficArea;

        return $this;
    }

    /**
     * Get the traffic area
     *
     * @return \Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea
     */
    public function getTrafficArea()
    {
        return $this->trafficArea;
    }

    /**
     * Set the version
     *
     * @param int $version
     * @return VoidDisc
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
