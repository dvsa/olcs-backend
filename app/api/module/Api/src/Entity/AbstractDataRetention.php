<?php

namespace Dvsa\Olcs\Api\Entity;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * DataRetention Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="data_retention",
 *    indexes={
 *        @ORM\Index(name="ix_entity_name", columns={"entity_name"}),
 *        @ORM\Index(name="ix_data_retention_rule_id", columns={"data_retention_rule_id"}),
 *        @ORM\Index(name="ix_delete_confirmation", columns={"action_confirmation"}),
 *        @ORM\Index(name="ix_deleted_date", columns={"deleted_date"}),
 *        @ORM\Index(name="ix_data_retention_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_data_retention_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_entity_name_entity_pk", columns={"entity_name","entity_pk"})
 *    }
 * )
 */
abstract class AbstractDataRetention implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;

    /**
     * Action after date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="action_after_date", nullable=true)
     */
    protected $actionAfterDate;

    /**
     * Action confirmation
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="action_confirmation", nullable=false)
     */
    protected $actionConfirmation;

    /**
     * Actioned date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="actioned_date", nullable=true)
     */
    protected $actionedDate;

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
     * Data retention rule
     *
     * @var \Dvsa\Olcs\Api\Entity\DataRetentionRule
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\DataRetentionRule", fetch="LAZY")
     * @ORM\JoinColumn(name="data_retention_rule_id", referencedColumnName="id", nullable=false)
     */
    protected $dataRetentionRule;

    /**
     * Deleted date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="deleted_date", nullable=true)
     */
    protected $deletedDate;

    /**
     * Entity name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="entity_name", length=64, nullable=false)
     */
    protected $entityName;

    /**
     * Entity pk
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="entity_pk", nullable=false)
     */
    protected $entityPk;

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
     * Last modified on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="last_modified_on", nullable=true)
     */
    protected $lastModifiedOn;

    /**
     * To action
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="to_action", nullable=false)
     */
    protected $toAction;

    /**
     * Set the action after date
     *
     * @param \DateTime $actionAfterDate new value being set
     *
     * @return DataRetention
     */
    public function setActionAfterDate($actionAfterDate)
    {
        $this->actionAfterDate = $actionAfterDate;

        return $this;
    }

    /**
     * Get the action after date
     *
     * @return \DateTime
     */
    public function getActionAfterDate()
    {
        return $this->actionAfterDate;
    }

    /**
     * Set the action confirmation
     *
     * @param boolean $actionConfirmation new value being set
     *
     * @return DataRetention
     */
    public function setActionConfirmation($actionConfirmation)
    {
        $this->actionConfirmation = $actionConfirmation;

        return $this;
    }

    /**
     * Get the action confirmation
     *
     * @return boolean
     */
    public function getActionConfirmation()
    {
        return $this->actionConfirmation;
    }

    /**
     * Set the actioned date
     *
     * @param \DateTime $actionedDate new value being set
     *
     * @return DataRetention
     */
    public function setActionedDate($actionedDate)
    {
        $this->actionedDate = $actionedDate;

        return $this;
    }

    /**
     * Get the actioned date
     *
     * @return \DateTime
     */
    public function getActionedDate()
    {
        return $this->actionedDate;
    }

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy entity being set as the value
     *
     * @return DataRetention
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
     * @return DataRetention
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get the created on
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set the data retention rule
     *
     * @param \Dvsa\Olcs\Api\Entity\DataRetentionRule $dataRetentionRule entity being set as the value
     *
     * @return DataRetention
     */
    public function setDataRetentionRule($dataRetentionRule)
    {
        $this->dataRetentionRule = $dataRetentionRule;

        return $this;
    }

    /**
     * Get the data retention rule
     *
     * @return \Dvsa\Olcs\Api\Entity\DataRetentionRule
     */
    public function getDataRetentionRule()
    {
        return $this->dataRetentionRule;
    }

    /**
     * Set the deleted date
     *
     * @param \DateTime $deletedDate new value being set
     *
     * @return DataRetention
     */
    public function setDeletedDate($deletedDate)
    {
        $this->deletedDate = $deletedDate;

        return $this;
    }

    /**
     * Get the deleted date
     *
     * @return \DateTime
     */
    public function getDeletedDate()
    {
        return $this->deletedDate;
    }

    /**
     * Set the entity name
     *
     * @param string $entityName new value being set
     *
     * @return DataRetention
     */
    public function setEntityName($entityName)
    {
        $this->entityName = $entityName;

        return $this;
    }

    /**
     * Get the entity name
     *
     * @return string
     */
    public function getEntityName()
    {
        return $this->entityName;
    }

    /**
     * Set the entity pk
     *
     * @param int $entityPk new value being set
     *
     * @return DataRetention
     */
    public function setEntityPk($entityPk)
    {
        $this->entityPk = $entityPk;

        return $this;
    }

    /**
     * Get the entity pk
     *
     * @return int
     */
    public function getEntityPk()
    {
        return $this->entityPk;
    }

    /**
     * Set the id
     *
     * @param int $id new value being set
     *
     * @return DataRetention
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
     * @return DataRetention
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
     * @return DataRetention
     */
    public function setLastModifiedOn($lastModifiedOn)
    {
        $this->lastModifiedOn = $lastModifiedOn;

        return $this;
    }

    /**
     * Get the last modified on
     *
     * @return \DateTime
     */
    public function getLastModifiedOn()
    {
        return $this->lastModifiedOn;
    }

    /**
     * Set the to action
     *
     * @param boolean $toAction new value being set
     *
     * @return DataRetention
     */
    public function setToAction($toAction)
    {
        $this->toAction = $toAction;

        return $this;
    }

    /**
     * Get the to action
     *
     * @return boolean
     */
    public function getToAction()
    {
        return $this->toAction;
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

    /**
     * Clear properties
     *
     * @param array $properties array of properties
     *
     * @return void
     */
    public function clearProperties($properties = array())
    {
        foreach ($properties as $property) {

            if (property_exists($this, $property)) {
                $this->$property = null;
            }
        }
    }
}
