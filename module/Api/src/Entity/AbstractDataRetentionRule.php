<?php

namespace Dvsa\Olcs\Api\Entity;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Dvsa\Olcs\Api\Entity\Traits\ClearPropertiesTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * DataRetentionRule Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="data_retention_rule",
 *    indexes={
 *        @ORM\Index(name="fk_data_retention_rule_created_by_user_id", columns={"created_by"}),
 *        @ORM\Index(name="fk_data_retention_rule_last_modified_by_user_id",
     *     columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_data_retention_rule_action_type_ref_data_id", columns={"action_type"})
 *    }
 * )
 */
abstract class AbstractDataRetentionRule implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;
    use ClearPropertiesTrait;

    /**
     * Action type
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="action_type", referencedColumnName="id", nullable=false)
     */
    protected $actionType;

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
     * Custom procedure
     *
     * @var string
     *
     * @ORM\Column(type="string", name="custom_procedure", length=64, nullable=true)
     */
    protected $customProcedure;

    /**
     * Deleted date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="deleted_date", nullable=true)
     */
    protected $deletedDate;

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
     * Is custom rule
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_custom_rule", nullable=false, options={"default": 0})
     */
    protected $isCustomRule = 0;

    /**
     * Is enabled
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_enabled", nullable=false, options={"default": 0})
     */
    protected $isEnabled = 0;

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
     * Max data set
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="max_data_set", nullable=true)
     */
    protected $maxDataSet;

    /**
     * Populate procedure
     *
     * @var string
     *
     * @ORM\Column(type="string", name="populate_procedure", length=64, nullable=false)
     */
    protected $populateProcedure;

    /**
     * Retention period
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="retention_period", nullable=false)
     */
    protected $retentionPeriod;

    /**
     * Set the action type
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $actionType entity being set as the value
     *
     * @return DataRetentionRule
     */
    public function setActionType($actionType)
    {
        $this->actionType = $actionType;

        return $this;
    }

    /**
     * Get the action type
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getActionType()
    {
        return $this->actionType;
    }

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy entity being set as the value
     *
     * @return DataRetentionRule
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
     * @return DataRetentionRule
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
     * Set the custom procedure
     *
     * @param string $customProcedure new value being set
     *
     * @return DataRetentionRule
     */
    public function setCustomProcedure($customProcedure)
    {
        $this->customProcedure = $customProcedure;

        return $this;
    }

    /**
     * Get the custom procedure
     *
     * @return string
     */
    public function getCustomProcedure()
    {
        return $this->customProcedure;
    }

    /**
     * Set the deleted date
     *
     * @param \DateTime $deletedDate new value being set
     *
     * @return DataRetentionRule
     */
    public function setDeletedDate($deletedDate)
    {
        $this->deletedDate = $deletedDate;

        return $this;
    }

    /**
     * Get the deleted date
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getDeletedDate($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->deletedDate);
        }

        return $this->deletedDate;
    }

    /**
     * Set the description
     *
     * @param string $description new value being set
     *
     * @return DataRetentionRule
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
     * @return DataRetentionRule
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
     * Set the is custom rule
     *
     * @param boolean $isCustomRule new value being set
     *
     * @return DataRetentionRule
     */
    public function setIsCustomRule($isCustomRule)
    {
        $this->isCustomRule = $isCustomRule;

        return $this;
    }

    /**
     * Get the is custom rule
     *
     * @return boolean
     */
    public function getIsCustomRule()
    {
        return $this->isCustomRule;
    }

    /**
     * Set the is enabled
     *
     * @param boolean $isEnabled new value being set
     *
     * @return DataRetentionRule
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    /**
     * Get the is enabled
     *
     * @return boolean
     */
    public function getIsEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy entity being set as the value
     *
     * @return DataRetentionRule
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
     * @return DataRetentionRule
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
     * Set the max data set
     *
     * @param int $maxDataSet new value being set
     *
     * @return DataRetentionRule
     */
    public function setMaxDataSet($maxDataSet)
    {
        $this->maxDataSet = $maxDataSet;

        return $this;
    }

    /**
     * Get the max data set
     *
     * @return int
     */
    public function getMaxDataSet()
    {
        return $this->maxDataSet;
    }

    /**
     * Set the populate procedure
     *
     * @param string $populateProcedure new value being set
     *
     * @return DataRetentionRule
     */
    public function setPopulateProcedure($populateProcedure)
    {
        $this->populateProcedure = $populateProcedure;

        return $this;
    }

    /**
     * Get the populate procedure
     *
     * @return string
     */
    public function getPopulateProcedure()
    {
        return $this->populateProcedure;
    }

    /**
     * Set the retention period
     *
     * @param int $retentionPeriod new value being set
     *
     * @return DataRetentionRule
     */
    public function setRetentionPeriod($retentionPeriod)
    {
        $this->retentionPeriod = $retentionPeriod;

        return $this;
    }

    /**
     * Get the retention period
     *
     * @return int
     */
    public function getRetentionPeriod()
    {
        return $this->retentionPeriod;
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
