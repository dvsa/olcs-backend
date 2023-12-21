<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

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
 * IrhpPermitType Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="irhp_permit_type",
 *    indexes={
 *        @ORM\Index(name="fk_irhp_permit_type_created_by_user_id", columns={"created_by"}),
 *        @ORM\Index(name="fk_irhp_permit_type_last_modified_by_user_id",
     *     columns={"last_modified_by"}),
 *        @ORM\Index(name="irhp_permit_type_ref_data_id_fk", columns={"name"})
 *    }
 * )
 */
abstract class AbstractIrhpPermitType implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;
    use ClearPropertiesWithCollectionsTrait;
    use CreatedOnTrait;
    use ModifiedOnTrait;

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
     * Description
     *
     * @var string
     *
     * @ORM\Column(type="text", name="description", length=65535, nullable=true)
     */
    protected $description;

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
     * @Gedmo\Blameable(on="update")
     */
    protected $lastModifiedBy;

    /**
     * Name
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="name", referencedColumnName="id", nullable=false)
     */
    protected $name;

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
     * Application path
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Generic\ApplicationPath",
     *     mappedBy="irhpPermitType"
     * )
     */
    protected $applicationPaths;

    /**
     * Irhp permit stock
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock",
     *     mappedBy="irhpPermitType"
     * )
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
        $this->applicationPaths = new ArrayCollection();
        $this->irhpPermitStocks = new ArrayCollection();
    }

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy entity being set as the value
     *
     * @return IrhpPermitType
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
     * Set the description
     *
     * @param string $description new value being set
     *
     * @return IrhpPermitType
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the id
     *
     * @param int $id new value being set
     *
     * @return IrhpPermitType
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
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy entity being set as the value
     *
     * @return IrhpPermitType
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
     * Set the name
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $name entity being set as the value
     *
     * @return IrhpPermitType
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the name
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return IrhpPermitType
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
     * Set the application path
     *
     * @param ArrayCollection $applicationPaths collection being set as the value
     *
     * @return IrhpPermitType
     */
    public function setApplicationPaths($applicationPaths)
    {
        $this->applicationPaths = $applicationPaths;

        return $this;
    }

    /**
     * Get the application paths
     *
     * @return ArrayCollection
     */
    public function getApplicationPaths()
    {
        return $this->applicationPaths;
    }

    /**
     * Add a application paths
     *
     * @param ArrayCollection|mixed $applicationPaths collection being added
     *
     * @return IrhpPermitType
     */
    public function addApplicationPaths($applicationPaths)
    {
        if ($applicationPaths instanceof ArrayCollection) {
            $this->applicationPaths = new ArrayCollection(
                array_merge(
                    $this->applicationPaths->toArray(),
                    $applicationPaths->toArray()
                )
            );
        } elseif (!$this->applicationPaths->contains($applicationPaths)) {
            $this->applicationPaths->add($applicationPaths);
        }

        return $this;
    }

    /**
     * Remove a application paths
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $applicationPaths collection being removed
     *
     * @return IrhpPermitType
     */
    public function removeApplicationPaths($applicationPaths)
    {
        if ($this->applicationPaths->contains($applicationPaths)) {
            $this->applicationPaths->removeElement($applicationPaths);
        }

        return $this;
    }

    /**
     * Set the irhp permit stock
     *
     * @param ArrayCollection $irhpPermitStocks collection being set as the value
     *
     * @return IrhpPermitType
     */
    public function setIrhpPermitStocks($irhpPermitStocks)
    {
        $this->irhpPermitStocks = $irhpPermitStocks;

        return $this;
    }

    /**
     * Get the irhp permit stocks
     *
     * @return ArrayCollection
     */
    public function getIrhpPermitStocks()
    {
        return $this->irhpPermitStocks;
    }

    /**
     * Add a irhp permit stocks
     *
     * @param ArrayCollection|mixed $irhpPermitStocks collection being added
     *
     * @return IrhpPermitType
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
     * @return IrhpPermitType
     */
    public function removeIrhpPermitStocks($irhpPermitStocks)
    {
        if ($this->irhpPermitStocks->contains($irhpPermitStocks)) {
            $this->irhpPermitStocks->removeElement($irhpPermitStocks);
        }

        return $this;
    }
}
