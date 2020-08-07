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
 * ReplacementTagLink Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="replacement_tag_link",
 *    indexes={
 *        @ORM\Index(name="fk_replacement_tag_link_replacement1_idx", columns={"replacement_id"}),
 *        @ORM\Index(name="fk_replacement_tag_link_tags1_idx", columns={"tag_id"}),
 *        @ORM\Index(name="fk_replacement_tag_link_users_created_by", columns={"created_by"}),
 *        @ORM\Index(name="fk_replacement_tag_link_users_last_modified_by",
     *     columns={"last_modified_by"})
 *    }
 * )
 */
abstract class AbstractReplacementTagLink implements BundleSerializableInterface, JsonSerializable
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
     * Replacement
     *
     * @var \Dvsa\Olcs\Api\Entity\System\Replacement
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\Replacement", fetch="LAZY")
     * @ORM\JoinColumn(name="replacement_id", referencedColumnName="id", nullable=false)
     */
    protected $replacement;

    /**
     * Tag
     *
     * @var \Dvsa\Olcs\Api\Entity\System\Tag
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\Tag", fetch="LAZY")
     * @ORM\JoinColumn(name="tag_id", referencedColumnName="id", nullable=false)
     */
    protected $tag;

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
     * @return ReplacementTagLink
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
     * @return ReplacementTagLink
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
     * @return ReplacementTagLink
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
     * Set the replacement
     *
     * @param \Dvsa\Olcs\Api\Entity\System\Replacement $replacement entity being set as the value
     *
     * @return ReplacementTagLink
     */
    public function setReplacement($replacement)
    {
        $this->replacement = $replacement;

        return $this;
    }

    /**
     * Get the replacement
     *
     * @return \Dvsa\Olcs\Api\Entity\System\Replacement
     */
    public function getReplacement()
    {
        return $this->replacement;
    }

    /**
     * Set the tag
     *
     * @param \Dvsa\Olcs\Api\Entity\System\Tag $tag entity being set as the value
     *
     * @return ReplacementTagLink
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get the tag
     *
     * @return \Dvsa\Olcs\Api\Entity\System\Tag
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return ReplacementTagLink
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
