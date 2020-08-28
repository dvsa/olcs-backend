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
 * PartialTagLink Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="partial_tag_link",
 *    indexes={
 *        @ORM\Index(name="fk_partial_tag_link_partial1_idx", columns={"partial_id"}),
 *        @ORM\Index(name="fk_partial_tag_link_tags1_idx", columns={"tag_id"}),
 *        @ORM\Index(name="fk_partial_tag_link_users_created_by", columns={"created_by"}),
 *        @ORM\Index(name="fk_partial_tag_link_users_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
abstract class AbstractPartialTagLink implements BundleSerializableInterface, JsonSerializable
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
     * Partial
     *
     * @var \Dvsa\Olcs\Api\Entity\System\Partial
     *
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\System\Partial",
     *     fetch="LAZY",
     *     inversedBy="partialTagLinks"
     * )
     * @ORM\JoinColumn(name="partial_id", referencedColumnName="id", nullable=false)
     */
    protected $partial;

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
     * @return PartialTagLink
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
     * @return PartialTagLink
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
     * @return PartialTagLink
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
     * Set the partial
     *
     * @param \Dvsa\Olcs\Api\Entity\System\Partial $partial entity being set as the value
     *
     * @return PartialTagLink
     */
    public function setPartial($partial)
    {
        $this->partial = $partial;

        return $this;
    }

    /**
     * Get the partial
     *
     * @return \Dvsa\Olcs\Api\Entity\System\Partial
     */
    public function getPartial()
    {
        return $this->partial;
    }

    /**
     * Set the tag
     *
     * @param \Dvsa\Olcs\Api\Entity\System\Tag $tag entity being set as the value
     *
     * @return PartialTagLink
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
     * @return PartialTagLink
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
