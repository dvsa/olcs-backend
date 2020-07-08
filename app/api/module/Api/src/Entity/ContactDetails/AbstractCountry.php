<?php

namespace Dvsa\Olcs\Api\Entity\ContactDetails;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Dvsa\Olcs\Api\Entity\Traits\ClearPropertiesWithCollectionsTrait;
use Dvsa\Olcs\Api\Entity\Traits\CreatedOnTrait;
use Dvsa\Olcs\Api\Entity\Traits\ModifiedOnTrait;
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
    use ClearPropertiesWithCollectionsTrait;
    use CreatedOnTrait;
    use ModifiedOnTrait;

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
     * Irhp application
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Permits\IrhpApplication",
     *     mappedBy="countrys",
     *     fetch="LAZY"
     * )
     */
    protected $irhpApplications;

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
     * Is permit state
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_permit_state", nullable=false, options={"default": 0})
     */
    protected $isPermitState = 0;

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
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="version", nullable=false, options={"default": 1})
     * @ORM\Version
     */
    protected $version = 1;

    /**
     * Irhp permit stock
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock", mappedBy="country")
     */
    protected $irhpPermitStocks;

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
        $this->irfoPsvAuths = new ArrayCollection();
        $this->irhpApplications = new ArrayCollection();
        $this->irhpPermitStockRanges = new ArrayCollection();
        $this->irhpPermitStocks = new ArrayCollection();
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
     * Set the irhp application
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpApplications collection being set as the value
     *
     * @return Country
     */
    public function setIrhpApplications($irhpApplications)
    {
        $this->irhpApplications = $irhpApplications;

        return $this;
    }

    /**
     * Get the irhp applications
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getIrhpApplications()
    {
        return $this->irhpApplications;
    }

    /**
     * Add a irhp applications
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpApplications collection being added
     *
     * @return Country
     */
    public function addIrhpApplications($irhpApplications)
    {
        if ($irhpApplications instanceof ArrayCollection) {
            $this->irhpApplications = new ArrayCollection(
                array_merge(
                    $this->irhpApplications->toArray(),
                    $irhpApplications->toArray()
                )
            );
        } elseif (!$this->irhpApplications->contains($irhpApplications)) {
            $this->irhpApplications->add($irhpApplications);
        }

        return $this;
    }

    /**
     * Remove a irhp applications
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpApplications collection being removed
     *
     * @return Country
     */
    public function removeIrhpApplications($irhpApplications)
    {
        if ($this->irhpApplications->contains($irhpApplications)) {
            $this->irhpApplications->removeElement($irhpApplications);
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
     * Set the is permit state
     *
     * @param boolean $isPermitState new value being set
     *
     * @return Country
     */
    public function setIsPermitState($isPermitState)
    {
        $this->isPermitState = $isPermitState;

        return $this;
    }

    /**
     * Get the is permit state
     *
     * @return boolean
     */
    public function getIsPermitState()
    {
        return $this->isPermitState;
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
     * Set the irhp permit stock
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpPermitStocks collection being set as the value
     *
     * @return Country
     */
    public function setIrhpPermitStocks($irhpPermitStocks)
    {
        $this->irhpPermitStocks = $irhpPermitStocks;

        return $this;
    }

    /**
     * Get the irhp permit stocks
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getIrhpPermitStocks()
    {
        return $this->irhpPermitStocks;
    }

    /**
     * Add a irhp permit stocks
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpPermitStocks collection being added
     *
     * @return Country
     */
    public function addIrhpPermitStocks($irhpPermitStocks)
    {
        if ($irhpPermitStocks instanceof ArrayCollection) {
            $this->irhpPermitStocks = new ArrayCollection(
                array_merge(
                    $this->irhpPermitStocks->toArray(),
                    $irhpPermitStocks->toArray()
                )
            );
        } elseif (!$this->irhpPermitStocks->contains($irhpPermitStocks)) {
            $this->irhpPermitStocks->add($irhpPermitStocks);
        }

        return $this;
    }

    /**
     * Remove a irhp permit stocks
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpPermitStocks collection being removed
     *
     * @return Country
     */
    public function removeIrhpPermitStocks($irhpPermitStocks)
    {
        if ($this->irhpPermitStocks->contains($irhpPermitStocks)) {
            $this->irhpPermitStocks->removeElement($irhpPermitStocks);
        }

        return $this;
    }
}
