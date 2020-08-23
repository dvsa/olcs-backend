<?php

namespace Dvsa\Olcs\Api\Entity\System;

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
 * Partial Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="partial",
 *    indexes={
 *        @ORM\Index(name="fk_partial_users_created_by", columns={"created_by"}),
 *        @ORM\Index(name="fk_partial_users_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="partial_key", columns={"partial_key","prefix"})
 *    }
 * )
 */
abstract class AbstractPartial implements BundleSerializableInterface, JsonSerializable
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
     * @ORM\Column(type="string", name="description", length=512, nullable=true)
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
     * Partial key
     *
     * @var string
     *
     * @ORM\Column(type="string", name="partial_key", length=255, nullable=false)
     */
    protected $partialKey;

    /**
     * Prefix
     *
     * @var string
     *
     * @ORM\Column(type="string", name="prefix", length=255, nullable=true)
     */
    protected $prefix;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="version", nullable=true, options={"default": 1})
     * @ORM\Version
     */
    protected $version = 1;

    /**
     * Partial category link
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\System\PartialCategoryLink",
     *     mappedBy="partial"
     * )
     */
    protected $partialCategoryLinks;

    /**
     * Partial markup
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Dvsa\Olcs\Api\Entity\System\PartialMarkup", mappedBy="partial")
     */
    protected $partialMarkups;

    /**
     * Partial tag link
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Dvsa\Olcs\Api\Entity\System\PartialTagLink", mappedBy="partial")
     */
    protected $partialTagLinks;

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
        $this->partialCategoryLinks = new ArrayCollection();
        $this->partialMarkups = new ArrayCollection();
        $this->partialTagLinks = new ArrayCollection();
    }

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy entity being set as the value
     *
     * @return Partial
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
     * @return Partial
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
     * @return Partial
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
     * @return Partial
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
     * Set the partial key
     *
     * @param string $partialKey new value being set
     *
     * @return Partial
     */
    public function setPartialKey($partialKey)
    {
        $this->partialKey = $partialKey;

        return $this;
    }

    /**
     * Get the partial key
     *
     * @return string
     */
    public function getPartialKey()
    {
        return $this->partialKey;
    }

    /**
     * Set the prefix
     *
     * @param string $prefix new value being set
     *
     * @return Partial
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Get the prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return Partial
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
     * Set the partial category link
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $partialCategoryLinks collection being set as the value
     *
     * @return Partial
     */
    public function setPartialCategoryLinks($partialCategoryLinks)
    {
        $this->partialCategoryLinks = $partialCategoryLinks;

        return $this;
    }

    /**
     * Get the partial category links
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPartialCategoryLinks()
    {
        return $this->partialCategoryLinks;
    }

    /**
     * Add a partial category links
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $partialCategoryLinks collection being added
     *
     * @return Partial
     */
    public function addPartialCategoryLinks($partialCategoryLinks)
    {
        if ($partialCategoryLinks instanceof ArrayCollection) {
            $this->partialCategoryLinks = new ArrayCollection(
                array_merge(
                    $this->partialCategoryLinks->toArray(),
                    $partialCategoryLinks->toArray()
                )
            );
        } elseif (!$this->partialCategoryLinks->contains($partialCategoryLinks)) {
            $this->partialCategoryLinks->add($partialCategoryLinks);
        }

        return $this;
    }

    /**
     * Remove a partial category links
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $partialCategoryLinks collection being removed
     *
     * @return Partial
     */
    public function removePartialCategoryLinks($partialCategoryLinks)
    {
        if ($this->partialCategoryLinks->contains($partialCategoryLinks)) {
            $this->partialCategoryLinks->removeElement($partialCategoryLinks);
        }

        return $this;
    }

    /**
     * Set the partial markup
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $partialMarkups collection being set as the value
     *
     * @return Partial
     */
    public function setPartialMarkups($partialMarkups)
    {
        $this->partialMarkups = $partialMarkups;

        return $this;
    }

    /**
     * Get the partial markups
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPartialMarkups()
    {
        return $this->partialMarkups;
    }

    /**
     * Add a partial markups
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $partialMarkups collection being added
     *
     * @return Partial
     */
    public function addPartialMarkups($partialMarkups)
    {
        if ($partialMarkups instanceof ArrayCollection) {
            $this->partialMarkups = new ArrayCollection(
                array_merge(
                    $this->partialMarkups->toArray(),
                    $partialMarkups->toArray()
                )
            );
        } elseif (!$this->partialMarkups->contains($partialMarkups)) {
            $this->partialMarkups->add($partialMarkups);
        }

        return $this;
    }

    /**
     * Remove a partial markups
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $partialMarkups collection being removed
     *
     * @return Partial
     */
    public function removePartialMarkups($partialMarkups)
    {
        if ($this->partialMarkups->contains($partialMarkups)) {
            $this->partialMarkups->removeElement($partialMarkups);
        }

        return $this;
    }

    /**
     * Set the partial tag link
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $partialTagLinks collection being set as the value
     *
     * @return Partial
     */
    public function setPartialTagLinks($partialTagLinks)
    {
        $this->partialTagLinks = $partialTagLinks;

        return $this;
    }

    /**
     * Get the partial tag links
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPartialTagLinks()
    {
        return $this->partialTagLinks;
    }

    /**
     * Add a partial tag links
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $partialTagLinks collection being added
     *
     * @return Partial
     */
    public function addPartialTagLinks($partialTagLinks)
    {
        if ($partialTagLinks instanceof ArrayCollection) {
            $this->partialTagLinks = new ArrayCollection(
                array_merge(
                    $this->partialTagLinks->toArray(),
                    $partialTagLinks->toArray()
                )
            );
        } elseif (!$this->partialTagLinks->contains($partialTagLinks)) {
            $this->partialTagLinks->add($partialTagLinks);
        }

        return $this;
    }

    /**
     * Remove a partial tag links
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $partialTagLinks collection being removed
     *
     * @return Partial
     */
    public function removePartialTagLinks($partialTagLinks)
    {
        if ($this->partialTagLinks->contains($partialTagLinks)) {
            $this->partialTagLinks->removeElement($partialTagLinks);
        }

        return $this;
    }
}
