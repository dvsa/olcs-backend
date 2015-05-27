<?php

namespace Dvsa\Olcs\Api\Entity\Licence;

use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\JsonSerializableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * GracePeriod Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="grace_period",
 *    indexes={
 *        @ORM\Index(name="ix_grace_period_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_grace_period_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_grace_period_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_grace_period_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
abstract class AbstractGracePeriod implements \JsonSerializable
{
    use JsonSerializableTrait;

    /**
     * Created by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
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
     * @ORM\Column(type="string", name="description", length=90, nullable=false)
     */
    protected $description;

    /**
     * End date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="end_date", nullable=false)
     */
    protected $endDate;

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
     * Licence
     *
     * @var \Dvsa\Olcs\Api\Entity\Licence\Licence
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Licence\Licence", fetch="LAZY")
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id", nullable=false)
     */
    protected $licence;

    /**
     * Olbs key
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="olbs_key", nullable=true)
     */
    protected $olbsKey;

    /**
     * Start date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="start_date", nullable=false)
     */
    protected $startDate;

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
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy
     * @return GracePeriod
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
     * @param \DateTime $createdOn
     * @return GracePeriod
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
     * Set the description
     *
     * @param string $description
     * @return GracePeriod
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
     * Set the end date
     *
     * @param \DateTime $endDate
     * @return GracePeriod
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get the end date
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set the id
     *
     * @param int $id
     * @return GracePeriod
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
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy
     * @return GracePeriod
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
     * @param \DateTime $lastModifiedOn
     * @return GracePeriod
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
     * Set the licence
     *
     * @param \Dvsa\Olcs\Api\Entity\Licence\Licence $licence
     * @return GracePeriod
     */
    public function setLicence($licence)
    {
        $this->licence = $licence;

        return $this;
    }

    /**
     * Get the licence
     *
     * @return \Dvsa\Olcs\Api\Entity\Licence\Licence
     */
    public function getLicence()
    {
        return $this->licence;
    }

    /**
     * Set the olbs key
     *
     * @param int $olbsKey
     * @return GracePeriod
     */
    public function setOlbsKey($olbsKey)
    {
        $this->olbsKey = $olbsKey;

        return $this;
    }

    /**
     * Get the olbs key
     *
     * @return int
     */
    public function getOlbsKey()
    {
        return $this->olbsKey;
    }

    /**
     * Set the start date
     *
     * @param \DateTime $startDate
     * @return GracePeriod
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get the start date
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set the version
     *
     * @param int $version
     * @return GracePeriod
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
     */
    public function setCreatedOnBeforePersist()
    {
        $this->createdOn = new \DateTime();
    }

    /**
     * Set the lastModifiedOn field on persist
     *
     * @ORM\PreUpdate
     */
    public function setLastModifiedOnBeforeUpdate()
    {
        $this->lastModifiedOn = new \DateTime();
    }

    /**
     * Clear properties
     *
     * @param type $properties
     */
    public function clearProperties($properties = array())
    {
        foreach ($properties as $property) {

            if (property_exists($this, $property)) {
                if ($this->$property instanceof Collection) {

                    $this->$property = new ArrayCollection(array());

                } else {

                    $this->$property = null;
                }
            }
        }
    }
}
