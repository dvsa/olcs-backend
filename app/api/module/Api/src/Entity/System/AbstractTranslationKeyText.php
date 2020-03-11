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
 * TranslationKeyText Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="translation_key_text",
 *    indexes={
 *        @ORM\Index(name="fk_translation_key_text_keys1_idx", columns={"translation_key_id"}),
 *        @ORM\Index(name="fk_translation_key_text_languages1_idx", columns={"language_id"}),
 *        @ORM\Index(name="fk_translation_key_text_users_created_by", columns={"created_by"}),
 *        @ORM\Index(name="fk_translation_key_text_users_last_modified_by",
     *     columns={"last_modified_by"})
 *    }
 * )
 */
abstract class AbstractTranslationKeyText implements BundleSerializableInterface, JsonSerializable
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
     * Language
     *
     * @var \Dvsa\Olcs\Api\Entity\System\Language
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\Language", fetch="LAZY")
     * @ORM\JoinColumn(name="language_id", referencedColumnName="id", nullable=false)
     */
    protected $language;

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
     * Translated text
     *
     * @var string
     *
     * @ORM\Column(type="text", name="translated_text", length=65535, nullable=true)
     */
    protected $translatedText;

    /**
     * Translation key
     *
     * @var \Dvsa\Olcs\Api\Entity\System\TranslationKey
     *
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\System\TranslationKey",
     *     fetch="LAZY",
     *     inversedBy="translationKeyTexts"
     * )
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
     * @return TranslationKeyText
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
     * @return TranslationKeyText
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
     * Set the language
     *
     * @param \Dvsa\Olcs\Api\Entity\System\Language $language entity being set as the value
     *
     * @return TranslationKeyText
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get the language
     *
     * @return \Dvsa\Olcs\Api\Entity\System\Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy entity being set as the value
     *
     * @return TranslationKeyText
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
     * Set the translated text
     *
     * @param string $translatedText new value being set
     *
     * @return TranslationKeyText
     */
    public function setTranslatedText($translatedText)
    {
        $this->translatedText = $translatedText;

        return $this;
    }

    /**
     * Get the translated text
     *
     * @return string
     */
    public function getTranslatedText()
    {
        return $this->translatedText;
    }

    /**
     * Set the translation key
     *
     * @param \Dvsa\Olcs\Api\Entity\System\TranslationKey $translationKey entity being set as the value
     *
     * @return TranslationKeyText
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
     * @return TranslationKeyText
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
