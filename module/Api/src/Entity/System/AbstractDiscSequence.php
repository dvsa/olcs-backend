<?php

namespace Dvsa\Olcs\Api\Entity\System;

use Doctrine\ORM\Mapping as ORM;

/**
 * DiscSequence Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="disc_sequence",
 *    indexes={
 *        @ORM\Index(name="ix_disc_sequence_goods_or_psv", columns={"goods_or_psv"}),
 *        @ORM\Index(name="ix_disc_sequence_traffic_area_id", columns={"traffic_area_id"}),
 *        @ORM\Index(name="ix_disc_sequence_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_disc_sequence_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
abstract class AbstractDiscSequence
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
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_ni_self_serve", nullable=false, options={"default": 0})
     */
    protected $isNiSelfServe = 0;

    /**
     * Is self serve
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_self_serve", nullable=false, options={"default": 0})
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
     * R prefix
     *
     * @var string
     *
     * @ORM\Column(type="string", name="r_prefix", length=3, nullable=true)
     */
    protected $rPrefix;

    /**
     * Restricted
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="restricted", nullable=true)
     */
    protected $restricted;

    /**
     * Si prefix
     *
     * @var string
     *
     * @ORM\Column(type="string", name="si_prefix", length=3, nullable=true)
     */
    protected $siPrefix;

    /**
     * Sn prefix
     *
     * @var string
     *
     * @ORM\Column(type="string", name="sn_prefix", length=3, nullable=true)
     */
    protected $snPrefix;

    /**
     * Special restricted
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="special_restricted", nullable=true)
     */
    protected $specialRestricted;

    /**
     * Sr prefix
     *
     * @var string
     *
     * @ORM\Column(type="string", name="sr_prefix", length=3, nullable=true)
     */
    protected $srPrefix;

    /**
     * Standard international
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="standard_international", nullable=true)
     */
    protected $standardInternational;

    /**
     * Standard national
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="standard_national", nullable=true)
     */
    protected $standardNational;

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
     * @ORM\Version
     * @ORM\Column(type="smallint", name="version", nullable=false, options={"default": 1})
     */
    protected $version = 1;

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy
     * @return DiscSequence
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
     * @return DiscSequence
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
     * @return DiscSequence
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
     * @return DiscSequence
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
     * @param string $isNiSelfServe
     * @return DiscSequence
     */
    public function setIsNiSelfServe($isNiSelfServe)
    {
        $this->isNiSelfServe = $isNiSelfServe;

        return $this;
    }

    /**
     * Get the is ni self serve
     *
     * @return string
     */
    public function getIsNiSelfServe()
    {
        return $this->isNiSelfServe;
    }

    /**
     * Set the is self serve
     *
     * @param string $isSelfServe
     * @return DiscSequence
     */
    public function setIsSelfServe($isSelfServe)
    {
        $this->isSelfServe = $isSelfServe;

        return $this;
    }

    /**
     * Get the is self serve
     *
     * @return string
     */
    public function getIsSelfServe()
    {
        return $this->isSelfServe;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy
     * @return DiscSequence
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
     * @return DiscSequence
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
     * Set the r prefix
     *
     * @param string $rPrefix
     * @return DiscSequence
     */
    public function setRPrefix($rPrefix)
    {
        $this->rPrefix = $rPrefix;

        return $this;
    }

    /**
     * Get the r prefix
     *
     * @return string
     */
    public function getRPrefix()
    {
        return $this->rPrefix;
    }

    /**
     * Set the restricted
     *
     * @param int $restricted
     * @return DiscSequence
     */
    public function setRestricted($restricted)
    {
        $this->restricted = $restricted;

        return $this;
    }

    /**
     * Get the restricted
     *
     * @return int
     */
    public function getRestricted()
    {
        return $this->restricted;
    }

    /**
     * Set the si prefix
     *
     * @param string $siPrefix
     * @return DiscSequence
     */
    public function setSiPrefix($siPrefix)
    {
        $this->siPrefix = $siPrefix;

        return $this;
    }

    /**
     * Get the si prefix
     *
     * @return string
     */
    public function getSiPrefix()
    {
        return $this->siPrefix;
    }

    /**
     * Set the sn prefix
     *
     * @param string $snPrefix
     * @return DiscSequence
     */
    public function setSnPrefix($snPrefix)
    {
        $this->snPrefix = $snPrefix;

        return $this;
    }

    /**
     * Get the sn prefix
     *
     * @return string
     */
    public function getSnPrefix()
    {
        return $this->snPrefix;
    }

    /**
     * Set the special restricted
     *
     * @param int $specialRestricted
     * @return DiscSequence
     */
    public function setSpecialRestricted($specialRestricted)
    {
        $this->specialRestricted = $specialRestricted;

        return $this;
    }

    /**
     * Get the special restricted
     *
     * @return int
     */
    public function getSpecialRestricted()
    {
        return $this->specialRestricted;
    }

    /**
     * Set the sr prefix
     *
     * @param string $srPrefix
     * @return DiscSequence
     */
    public function setSrPrefix($srPrefix)
    {
        $this->srPrefix = $srPrefix;

        return $this;
    }

    /**
     * Get the sr prefix
     *
     * @return string
     */
    public function getSrPrefix()
    {
        return $this->srPrefix;
    }

    /**
     * Set the standard international
     *
     * @param int $standardInternational
     * @return DiscSequence
     */
    public function setStandardInternational($standardInternational)
    {
        $this->standardInternational = $standardInternational;

        return $this;
    }

    /**
     * Get the standard international
     *
     * @return int
     */
    public function getStandardInternational()
    {
        return $this->standardInternational;
    }

    /**
     * Set the standard national
     *
     * @param int $standardNational
     * @return DiscSequence
     */
    public function setStandardNational($standardNational)
    {
        $this->standardNational = $standardNational;

        return $this;
    }

    /**
     * Get the standard national
     *
     * @return int
     */
    public function getStandardNational()
    {
        return $this->standardNational;
    }

    /**
     * Set the traffic area
     *
     * @param \Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea $trafficArea
     * @return DiscSequence
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
     * @return DiscSequence
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
