<?php

namespace Dvsa\Olcs\Api\Entity;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
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
     *     columns={"last_modified_by"})
 *    }
 * )
 */
abstract class AbstractDataRetentionRule implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;

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
     * Delete procedure
     *
     * @var string
     *
     * @ORM\Column(type="string", name="delete_procedure", length=64, nullable=false)
     */
    protected $deleteProcedure;

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
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="retention_period", nullable=false)
     */
    protected $retentionPeriod;

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
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set the delete procedure
     *
     * @param string $deleteProcedure new value being set
     *
     * @return DataRetentionRule
     */
    public function setDeleteProcedure($deleteProcedure)
    {
        $this->deleteProcedure = $deleteProcedure;

        return $this;
    }

    /**
     * Get the delete procedure
     *
     * @return string
     */
    public function getDeleteProcedure()
    {
        return $this->deleteProcedure;
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
     * @return \DateTime
     */
    public function getDeletedDate()
    {
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
     * @return \DateTime
     */
    public function getLastModifiedOn()
    {
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
     * @param boolean $retentionPeriod new value being set
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
     * @return boolean
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
