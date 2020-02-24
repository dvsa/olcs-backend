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
 * TranslationKey Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="translation_key",
 *    indexes={
 *        @ORM\Index(name="fk_translation_key_users_created_by", columns={"created_by"}),
 *        @ORM\Index(name="fk_translation_key_users_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
abstract class AbstractTranslationKey implements BundleSerializableInterface, JsonSerializable
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
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string", name="id", length=255)
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
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="version", nullable=true, options={"default": 1})
     * @ORM\Version
     */
    protected $version = 1;

    /**
     * Translation key category link
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\System\TranslationKeyCategoryLink",
     *     mappedBy="translationKey"
     * )
     */
    protected $translationKeyCategoryLinks;

    /**
     * Translation key text
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\System\TranslationKeyText",
     *     mappedBy="translationKey"
     * )
     */
    protected $translationKeyTexts;

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
        $this->translationKeyCategoryLinks = new ArrayCollection();
        $this->translationKeyTexts = new ArrayCollection();
    }

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy entity being set as the value
     *
     * @return TranslationKey
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
     * @return TranslationKey
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
     * @param string $id new value being set
     *
     * @return TranslationKey
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
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy entity being set as the value
     *
     * @return TranslationKey
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
     * @return TranslationKey
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
     * Set the translation key category link
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $translationKeyCategoryLinks collection being set as the value
     *
     * @return TranslationKey
     */
    public function setTranslationKeyCategoryLinks($translationKeyCategoryLinks)
    {
        $this->translationKeyCategoryLinks = $translationKeyCategoryLinks;

        return $this;
    }

    /**
     * Get the translation key category links
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTranslationKeyCategoryLinks()
    {
        return $this->translationKeyCategoryLinks;
    }

    /**
     * Add a translation key category links
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $translationKeyCategoryLinks collection being added
     *
     * @return TranslationKey
     */
    public function addTranslationKeyCategoryLinks($translationKeyCategoryLinks)
    {
        if ($translationKeyCategoryLinks instanceof ArrayCollection) {
            $this->translationKeyCategoryLinks = new ArrayCollection(
                array_merge(
                    $this->translationKeyCategoryLinks->toArray(),
                    $translationKeyCategoryLinks->toArray()
                )
            );
        } elseif (!$this->translationKeyCategoryLinks->contains($translationKeyCategoryLinks)) {
            $this->translationKeyCategoryLinks->add($translationKeyCategoryLinks);
        }

        return $this;
    }

    /**
     * Remove a translation key category links
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $translationKeyCategoryLinks collection being removed
     *
     * @return TranslationKey
     */
    public function removeTranslationKeyCategoryLinks($translationKeyCategoryLinks)
    {
        if ($this->translationKeyCategoryLinks->contains($translationKeyCategoryLinks)) {
            $this->translationKeyCategoryLinks->removeElement($translationKeyCategoryLinks);
        }

        return $this;
    }

    /**
     * Set the translation key text
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $translationKeyTexts collection being set as the value
     *
     * @return TranslationKey
     */
    public function setTranslationKeyTexts($translationKeyTexts)
    {
        $this->translationKeyTexts = $translationKeyTexts;

        return $this;
    }

    /**
     * Get the translation key texts
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTranslationKeyTexts()
    {
        return $this->translationKeyTexts;
    }

    /**
     * Add a translation key texts
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $translationKeyTexts collection being added
     *
     * @return TranslationKey
     */
    public function addTranslationKeyTexts($translationKeyTexts)
    {
        if ($translationKeyTexts instanceof ArrayCollection) {
            $this->translationKeyTexts = new ArrayCollection(
                array_merge(
                    $this->translationKeyTexts->toArray(),
                    $translationKeyTexts->toArray()
                )
            );
        } elseif (!$this->translationKeyTexts->contains($translationKeyTexts)) {
            $this->translationKeyTexts->add($translationKeyTexts);
        }

        return $this;
    }

    /**
     * Remove a translation key texts
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $translationKeyTexts collection being removed
     *
     * @return TranslationKey
     */
    public function removeTranslationKeyTexts($translationKeyTexts)
    {
        if ($this->translationKeyTexts->contains($translationKeyTexts)) {
            $this->translationKeyTexts->removeElement($translationKeyTexts);
        }

        return $this;
    }
}
