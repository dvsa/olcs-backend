<?php

namespace Dvsa\Olcs\Api\Entity\ContactDetails;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Country Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="country",
 *    indexes={
 *        @ORM\Index(name="ix_country_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_country_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
abstract class AbstractCountry implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;

    /**
     * Constraint
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\System\RefData",
     *     inversedBy="countrys",
     *     fetch="LAZY"
     * )
     * @ORM\JoinTable(name="ecmt_countries_constraints_link",
     *     joinColumns={
     *         @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="constraint_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $constraints;

    /**
     * Country desc
     *
     * @var string
     *
     * @ORM\Column(type="string", name="country_desc", length=50, nullable=true)
     */
    protected $countryDesc;

    /**
     * Created by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     * @Gedmo\Blameable(on="create")
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
     * Ecmt application
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication",
     *     mappedBy="countrys",
     *     fetch="LAZY"
     * )
     */
    protected $ecmtApplications;

    /**
     * Identifier - Id
     *
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string", name="id", length=2)
     */
    protected $id;

    /**
     * Irfo psv auth
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuth",
     *     mappedBy="countrys",
     *     fetch="LAZY"
     * )
     */
    protected $irfoPsvAuths;

    /**
     * Irhp permit stock range
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange",
     *     mappedBy="countrys",
     *     fetch="LAZY"
     * )
     */
    protected $irhpPermitStockRanges;

    /**
     * Is ecmt state
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_ecmt_state", nullable=true, options={"default": 0})
     */
    protected $isEcmtState = 0;

    /**
     * Is eea state
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_eea_state", nullable=false, options={"default": 0})
     */
    protected $isEeaState = 0;

    /**
     * Is member state
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_member_state", nullable=false, options={"default": 0})
     */
    protected $isMemberState = 0;

    /**
     * Last modified by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
     * @ORM\JoinColumn(name="last_modified_by", referencedColumnName="id", nullable=true)
     * @Gedmo\Blameable(on="update")
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
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="version", nullable=false, options={"default": 1})
     * @ORM\Version
     */
    protected $version = 1;

    /**
     * Initialise the collections
     *
     * @return void
     */
    public function __construct()
    {
        $this->initCollections();
    }

    /**
     * Initialise the collections
     *
     * @return void
     */
    public function initCollections()
    {
        $this->ecmtApplications = new ArrayCollection();
        $this->constraints = new ArrayCollection();
        $this->irfoPsvAuths = new ArrayCollection();
        $this->irhpPermitStockRanges = new ArrayCollection();
    }

    /**
     * Set the constraint
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $constraints collection being set as the value
     *
     * @return Country
     */
    public function setConstraints($constraints)
    {
        $this->constraints = $constraints;

        return $this;
    }

    /**
     * Get the constraints
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getConstraints()
    {
        return $this->constraints;
    }

    /**
     * Add a constraints
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $constraints collection being added
     *
     * @return Country
     */
    public function addConstraints($constraints)
    {
        if ($constraints instanceof ArrayCollection) {
            $this->constraints = new ArrayCollection(
                array_merge(
                    $this->constraints->toArray(),
                    $constraints->toArray()
                )
            );
        } elseif (!$this->constraints->contains($constraints)) {
            $this->constraints->add($constraints);
        }

        return $this;
    }

    /**
     * Remove a constraints
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $constraints collection being removed
     *
     * @return Country
     */
    public function removeConstraints($constraints)
    {
        if ($this->constraints->contains($constraints)) {
            $this->constraints->removeElement($constraints);
        }

        return $this;
    }

    /**
     * Set the country desc
     *
     * @param string $countryDesc new value being set
     *
     * @return Country
     */
    public function setCountryDesc($countryDesc)
    {
        $this->countryDesc = $countryDesc;

        return $this;
    }

    /**
     * Get the country desc
     *
     * @return string
     */
    public function getCountryDesc()
    {
        return $this->countryDesc;
    }

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy entity being set as the value
     *
     * @return Country
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
     * @param \DateTime $createdOn new value being set
     *
     * @return Country
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get the created on
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getCreatedOn($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->createdOn);
        }

        return $this->createdOn;
    }

    /**
     * Set the ecmt application
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $ecmtApplications collection being set as the value
     *
     * @return Country
     */
    public function setEcmtApplications($ecmtApplications)
    {
        $this->ecmtApplications = $ecmtApplications;

        return $this;
    }

    /**
     * Get the ecmt applications
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getEcmtApplications()
    {
        return $this->ecmtApplications;
    }

    /**
     * Add a ecmt applications
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $ecmtApplications collection being added
     *
     * @return Country
     */
    public function addEcmtApplications($ecmtApplications)
    {
        if ($ecmtApplications instanceof ArrayCollection) {
            $this->ecmtApplications = new ArrayCollection(
                array_merge(
                    $this->ecmtApplications->toArray(),
                    $ecmtApplications->toArray()
                )
            );
        } elseif (!$this->ecmtApplications->contains($ecmtApplications)) {
            $this->ecmtApplications->add($ecmtApplications);
        }

        return $this;
    }

    /**
     * Remove a ecmt applications
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $ecmtApplications collection being removed
     *
     * @return Country
     */
    public function removeEcmtApplications($ecmtApplications)
    {
        if ($this->ecmtApplications->contains($ecmtApplications)) {
            $this->ecmtApplications->removeElement($ecmtApplications);
        }

        return $this;
    }

    /**
     * Set the id
     *
     * @param string $id new value being set
     *
     * @return Country
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the irfo psv auth
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irfoPsvAuths collection being set as the value
     *
     * @return Country
     */
    public function setIrfoPsvAuths($irfoPsvAuths)
    {
        $this->irfoPsvAuths = $irfoPsvAuths;

        return $this;
    }

    /**
     * Get the irfo psv auths
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getIrfoPsvAuths()
    {
        return $this->irfoPsvAuths;
    }

    /**
     * Add a irfo psv auths
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irfoPsvAuths collection being added
     *
     * @return Country
     */
    public function addIrfoPsvAuths($irfoPsvAuths)
    {
        if ($irfoPsvAuths instanceof ArrayCollection) {
            $this->irfoPsvAuths = new ArrayCollection(
                array_merge(
                    $this->irfoPsvAuths->toArray(),
                    $irfoPsvAuths->toArray()
                )
            );
        } elseif (!$this->irfoPsvAuths->contains($irfoPsvAuths)) {
            $this->irfoPsvAuths->add($irfoPsvAuths);
        }

        return $this;
    }

    /**
     * Remove a irfo psv auths
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irfoPsvAuths collection being removed
     *
     * @return Country
     */
    public function removeIrfoPsvAuths($irfoPsvAuths)
    {
        if ($this->irfoPsvAuths->contains($irfoPsvAuths)) {
            $this->irfoPsvAuths->removeElement($irfoPsvAuths);
        }

        return $this;
    }

    /**
     * Set the irhp permit stock range
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpPermitStockRanges collection being set as the value
     *
     * @return Country
     */
    public function setIrhpPermitStockRanges($irhpPermitStockRanges)
    {
        $this->irhpPermitStockRanges = $irhpPermitStockRanges;

        return $this;
    }

    /**
     * Get the irhp permit stock ranges
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getIrhpPermitStockRanges()
    {
        return $this->irhpPermitStockRanges;
    }

    /**
     * Add a irhp permit stock ranges
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpPermitStockRanges collection being added
     *
     * @return Country
     */
    public function addIrhpPermitStockRanges($irhpPermitStockRanges)
    {
        if ($irhpPermitStockRanges instanceof ArrayCollection) {
            $this->irhpPermitStockRanges = new ArrayCollection(
                array_merge(
                    $this->irhpPermitStockRanges->toArray(),
                    $irhpPermitStockRanges->toArray()
                )
            );
        } elseif (!$this->irhpPermitStockRanges->contains($irhpPermitStockRanges)) {
            $this->irhpPermitStockRanges->add($irhpPermitStockRanges);
        }

        return $this;
    }

    /**
     * Remove a irhp permit stock ranges
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpPermitStockRanges collection being removed
     *
     * @return Country
     */
    public function removeIrhpPermitStockRanges($irhpPermitStockRanges)
    {
        if ($this->irhpPermitStockRanges->contains($irhpPermitStockRanges)) {
            $this->irhpPermitStockRanges->removeElement($irhpPermitStockRanges);
        }

        return $this;
    }

    /**
     * Set the is ecmt state
     *
     * @param boolean $isEcmtState new value being set
     *
     * @return Country
     */
    public function setIsEcmtState($isEcmtState)
    {
        $this->isEcmtState = $isEcmtState;

        return $this;
    }

    /**
     * Get the is ecmt state
     *
     * @return boolean
     */
    public function getIsEcmtState()
    {
        return $this->isEcmtState;
    }

    /**
     * Set the is eea state
     *
     * @param boolean $isEeaState new value being set
     *
     * @return Country
     */
    public function setIsEeaState($isEeaState)
    {
        $this->isEeaState = $isEeaState;

        return $this;
    }

    /**
     * Get the is eea state
     *
     * @return boolean
     */
    public function getIsEeaState()
    {
        return $this->isEeaState;
    }

    /**
     * Set the is member state
     *
     * @param string $isMemberState new value being set
     *
     * @return Country
     */
    public function setIsMemberState($isMemberState)
    {
        $this->isMemberState = $isMemberState;

        return $this;
    }

    /**
     * Get the is member state
     *
     * @return string
     */
    public function getIsMemberState()
    {
        return $this->isMemberState;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy entity being set as the value
     *
     * @return Country
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
     * @param \DateTime $lastModifiedOn new value being set
     *
     * @return Country
     */
    public function setLastModifiedOn($lastModifiedOn)
    {
        $this->lastModifiedOn = $lastModifiedOn;

        return $this;
    }

    /**
     * Get the last modified on
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getLastModifiedOn($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->lastModifiedOn);
        }

        return $this->lastModifiedOn;
    }

    /**
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return Country
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
     *
     * @return void
     */
    public function setCreatedOnBeforePersist()
    {
        $this->createdOn = new \DateTime();
    }

    /**
     * Set the lastModifiedOn field on persist
     *
     * @ORM\PreUpdate
     *
     * @return void
     */
    public function setLastModifiedOnBeforeUpdate()
    {
        $this->lastModifiedOn = new \DateTime();
    }

    /**
     * Clear properties
     *
     * @param array $properties array of properties
     *
     * @return void
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
