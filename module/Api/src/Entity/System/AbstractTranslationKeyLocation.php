<?php

namespace Dvsa\Olcs\Api\Entity\System;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Dvsa\Olcs\Api\Entity\Traits\ClearPropertiesTrait;
use Dvsa\Olcs\Api\Entity\Traits\CreatedOnTrait;
use Dvsa\Olcs\Api\Entity\Traits\ModifiedOnTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * TranslationKeyLocation Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="translation_key_location",
 *    indexes={
 *        @ORM\Index(name="fk_translation_key_location_translation_key1_idx",
     *     columns={"translation_key_id"}),
 *        @ORM\Index(name="fk_translation_key_location_users_created_by", columns={"created_by"}),
 *        @ORM\Index(name="fk_translation_key_location_users_last_modified_by",
     *     columns={"last_modified_by"})
 *    }
 * )
 */
abstract class AbstractTranslationKeyLocation implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;
    use ClearPropertiesTrait;
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
     * Path
     *
     * @var string
     *
     * @ORM\Column(type="string", name="path", length=255, nullable=true)
     */
    protected $path;

    /**
     * Repository
     *
     * @var string
     *
     * @ORM\Column(type="string", name="repository", length=255, nullable=true)
     */
    protected $repository;

    /**
     * Translation key
     *
     * @var \Dvsa\Olcs\Api\Entity\System\TranslationKey
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\TranslationKey", fetch="LAZY")
     * @ORM\JoinColumn(name="translation_key_id", referencedColumnName="id", nullable=false)
     */
    protected $translationKey;

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
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy entity being set as the value
     *
     * @return TranslationKeyLocation
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
     * @param int $id new value being set
     *
     * @return TranslationKeyLocation
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
     * @return TranslationKeyLocation
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
     * Set the path
     *
     * @param string $path new value being set
     *
     * @return TranslationKeyLocation
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get the path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the repository
     *
     * @param string $repository new value being set
     *
     * @return TranslationKeyLocation
     */
    public function setRepository($repository)
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * Get the repository
     *
     * @return string
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Set the translation key
     *
     * @param \Dvsa\Olcs\Api\Entity\System\TranslationKey $translationKey entity being set as the value
     *
     * @return TranslationKeyLocation
     */
    public function setTranslationKey($translationKey)
    {
        $this->translationKey = $translationKey;

        return $this;
    }

    /**
     * Get the translation key
     *
     * @return \Dvsa\Olcs\Api\Entity\System\TranslationKey
     */
    public function getTranslationKey()
    {
        return $this->translationKey;
    }

    /**
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return TranslationKeyLocation
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
}
