<?php

namespace Dvsa\Olcs\Api\Entity\System;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Dvsa\Olcs\Api\Entity\Traits\ClearPropertiesTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Category Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="category",
 *    indexes={
 *        @ORM\Index(name="ix_category_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_category_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_category_task_allocation_type", columns={"task_allocation_type"})
 *    }
 * )
 */
abstract class AbstractCategory implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;
    use ClearPropertiesTrait;

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
     * Description
     *
     * @var string
     *
     * @ORM\Column(type="string", name="description", length=255, nullable=false)
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
     * Is doc category
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_doc_category", nullable=false, options={"default": 1})
     */
    protected $isDocCategory = 1;

    /**
     * Is scan category
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_scan_category", nullable=false, options={"default": 1})
     */
    protected $isScanCategory = 1;

    /**
     * Is task category
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_task_category", nullable=false, options={"default": 1})
     */
    protected $isTaskCategory = 1;

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
     * Task allocation type
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="task_allocation_type", referencedColumnName="id", nullable=true)
     */
    protected $taskAllocationType;

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
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy entity being set as the value
     *
     * @return Category
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
     * @return Category
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
     * Set the description
     *
     * @param string $description new value being set
     *
     * @return Category
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
     * @return Category
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
     * Set the is doc category
     *
     * @param string $isDocCategory new value being set
     *
     * @return Category
     */
    public function setIsDocCategory($isDocCategory)
    {
        $this->isDocCategory = $isDocCategory;

        return $this;
    }

    /**
     * Get the is doc category
     *
     * @return string
     */
    public function getIsDocCategory()
    {
        return $this->isDocCategory;
    }

    /**
     * Set the is scan category
     *
     * @param boolean $isScanCategory new value being set
     *
     * @return Category
     */
    public function setIsScanCategory($isScanCategory)
    {
        $this->isScanCategory = $isScanCategory;

        return $this;
    }

    /**
     * Get the is scan category
     *
     * @return boolean
     */
    public function getIsScanCategory()
    {
        return $this->isScanCategory;
    }

    /**
     * Set the is task category
     *
     * @param string $isTaskCategory new value being set
     *
     * @return Category
     */
    public function setIsTaskCategory($isTaskCategory)
    {
        $this->isTaskCategory = $isTaskCategory;

        return $this;
    }

    /**
     * Get the is task category
     *
     * @return string
     */
    public function getIsTaskCategory()
    {
        return $this->isTaskCategory;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy entity being set as the value
     *
     * @return Category
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
     * @return Category
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
     * Set the task allocation type
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $taskAllocationType entity being set as the value
     *
     * @return Category
     */
    public function setTaskAllocationType($taskAllocationType)
    {
        $this->taskAllocationType = $taskAllocationType;

        return $this;
    }

    /**
     * Get the task allocation type
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getTaskAllocationType()
    {
        return $this->taskAllocationType;
    }

    /**
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return Category
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
}
