<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DiscSequence Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="disc_sequence",
 *    indexes={
 *        @ORM\Index(name="fk_disc_sequence_ref_data1_idx", 
 *            columns={"goods_or_psv"}),
 *        @ORM\Index(name="fk_disc_sequence_traffic_area1_idx", 
 *            columns={"traffic_area_id"}),
 *        @ORM\Index(name="fk_disc_sequence_user1_idx", 
 *            columns={"created_by"}),
 *        @ORM\Index(name="fk_disc_sequence_user2_idx", 
 *            columns={"last_modified_by"})
 *    }
 * )
 */
class DiscSequence implements Interfaces\EntityInterface
{

    /**
     * Restricted
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="restricted", nullable=true)
     */
    protected $restricted;

    /**
     * Special restricted
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="special_restricted", nullable=true)
     */
    protected $specialRestricted;

    /**
     * Standard national
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="standard_national", nullable=true)
     */
    protected $standardNational;

    /**
     * Standard international
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="standard_international", nullable=true)
     */
    protected $standardInternational;

    /**
     * R prefix
     *
     * @var string
     *
     * @ORM\Column(type="string", name="r_prefix", length=3, nullable=true)
     */
    protected $rPrefix;

    /**
     * Sn prefix
     *
     * @var string
     *
     * @ORM\Column(type="string", name="sn_prefix", length=3, nullable=true)
     */
    protected $snPrefix;

    /**
     * Si prefix
     *
     * @var string
     *
     * @ORM\Column(type="string", name="si_prefix", length=3, nullable=true)
     */
    protected $siPrefix;

    /**
     * Sr prefix
     *
     * @var string
     *
     * @ORM\Column(type="string", name="sr_prefix", length=3, nullable=true)
     */
    protected $srPrefix;

    /**
     * Is self serve
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_self_serve", nullable=false)
     */
    protected $isSelfServe = 0;

    /**
     * Is ni self serve
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_ni_self_serve", nullable=false)
     */
    protected $isNiSelfServe = 0;

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
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="last_modified_by", referencedColumnName="id", nullable=true)
     */
    protected $lastModifiedBy;

    /**
     * Created by
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     */
    protected $createdBy;

    /**
     * Traffic area
     *
     * @var \Olcs\Db\Entity\TrafficArea
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TrafficArea", fetch="LAZY")
     * @ORM\JoinColumn(name="traffic_area_id", referencedColumnName="id", nullable=true)
     */
    protected $trafficArea;

    /**
     * Goods or psv
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="goods_or_psv", referencedColumnName="id", nullable=true)
     */
    protected $goodsOrPsv;

    /**
     * Created on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_on", nullable=true)
     */
    protected $createdOn;

    /**
     * Last modified on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="last_modified_on", nullable=true)
     */
    protected $lastModifiedOn;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="version", nullable=false)
     * @ORM\Version
     */
    protected $version;

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

    /**
     * Set the id
     *
     * @param int $id
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * @param \Olcs\Db\Entity\User $lastModifiedBy
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setLastModifiedBy($lastModifiedBy)
    {
        $this->lastModifiedBy = $lastModifiedBy;

        return $this;
    }

    /**
     * Get the last modified by
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getLastModifiedBy()
    {
        return $this->lastModifiedBy;
    }

    /**
     * Set the created by
     *
     * @param \Olcs\Db\Entity\User $createdBy
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get the created by
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set the traffic area
     *
     * @param \Olcs\Db\Entity\TrafficArea $trafficArea
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setTrafficArea($trafficArea)
    {
        $this->trafficArea = $trafficArea;

        return $this;
    }

    /**
     * Get the traffic area
     *
     * @return \Olcs\Db\Entity\TrafficArea
     */
    public function getTrafficArea()
    {
        return $this->trafficArea;
    }

    /**
     * Set the goods or psv
     *
     * @param \Olcs\Db\Entity\RefData $goodsOrPsv
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setGoodsOrPsv($goodsOrPsv)
    {
        $this->goodsOrPsv = $goodsOrPsv;

        return $this;
    }

    /**
     * Get the goods or psv
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getGoodsOrPsv()
    {
        return $this->goodsOrPsv;
    }

    /**
     * Set the created on
     *
     * @param \DateTime $createdOn
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the createdOn field on persist
     *
     * @ORM\PrePersist
     */
    public function setCreatedOnBeforePersist()
    {
        $this->setCreatedOn(new \DateTime('NOW'));
    }

    /**
     * Set the last modified on
     *
     * @param \DateTime $lastModifiedOn
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the lastModifiedOn field on persist
     *
     * @ORM\PreUpdate
     */
    public function setLastModifiedOnBeforeUpdate()
    {
        $this->setLastModifiedOn(new \DateTime('NOW'));
    }

    /**
     * Set the version
     *
     * @param int $version
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the version field on persist
     *
     * @ORM\PrePersist
     */
    public function setVersionBeforePersist()
    {
        $this->setVersion(1);
    }
}
