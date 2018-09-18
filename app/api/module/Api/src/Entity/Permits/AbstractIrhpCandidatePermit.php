<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * IrhpCandidatePermit Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="irhp_candidate_permit",
 *    indexes={
 *        @ORM\Index(name="fk_irhp_candidate_permits_irhp_permit_applications1_idx",
     *     columns={"irhp_permit_application_id"}),
 *        @ORM\Index(name="fk_irhp_candidate_permit_irhp_permit_range",
     *     columns={"irhp_permit_range_id"}),
 *        @ORM\Index(name="fk_irhp_candidate_permit_created_by_user_id", columns={"created_by"}),
 *        @ORM\Index(name="fk_irhp_candidate_permit_last_modified_by_user_id",
     *     columns={"last_modified_by"})
 *    }
 * )
 */
abstract class AbstractIrhpCandidatePermit implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;

    /**
     * Application score
     *
     * @var float
     *
     * @ORM\Column(type="decimal", name="application_score", precision=18, scale=9, nullable=true)
     */
    protected $applicationScore;

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
     * Intensity of use
     *
     * @var float
     *
     * @ORM\Column(type="decimal", name="intensity_of_use", precision=18, scale=9, nullable=true)
     */
    protected $intensityOfUse;

    /**
     * Irhp permit application
     *
     * @var \Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication
     *
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication",
     *     fetch="LAZY",
     *     inversedBy="irhpCandidatePermits"
     * )
     * @ORM\JoinColumn(name="irhp_permit_application_id", referencedColumnName="id", nullable=false)
     */
    protected $irhpPermitApplication;

    /**
     * Irhp permit range
     *
     * @var \Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange", fetch="LAZY")
     * @ORM\JoinColumn(name="irhp_permit_range_id", referencedColumnName="id", nullable=true)
     */
    protected $irhpPermitRange;

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
     * Randomized score
     *
     * @var float
     *
     * @ORM\Column(type="decimal", name="randomized_score", precision=18, scale=9, nullable=true)
     */
    protected $randomizedScore;

    /**
     * Successful
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="successful", nullable=true, options={"default": 0})
     */
    protected $successful = 0;

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
     * Irhp permit
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Permits\IrhpPermit",
     *     mappedBy="irhpCandidatePermit"
     * )
     */
    protected $irhpPermits;

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
        $this->irhpPermits = new ArrayCollection();
    }

    /**
     * Set the application score
     *
     * @param float $applicationScore new value being set
     *
     * @return IrhpCandidatePermit
     */
    public function setApplicationScore($applicationScore)
    {
        $this->applicationScore = $applicationScore;

        return $this;
    }

    /**
     * Get the application score
     *
     * @return float
     */
    public function getApplicationScore()
    {
        return $this->applicationScore;
    }

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy entity being set as the value
     *
     * @return IrhpCandidatePermit
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
     * @return IrhpCandidatePermit
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
     * Set the id
     *
     * @param int $id new value being set
     *
     * @return IrhpCandidatePermit
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
     * Set the intensity of use
     *
     * @param float $intensityOfUse new value being set
     *
     * @return IrhpCandidatePermit
     */
    public function setIntensityOfUse($intensityOfUse)
    {
        $this->intensityOfUse = $intensityOfUse;

        return $this;
    }

    /**
     * Get the intensity of use
     *
     * @return float
     */
    public function getIntensityOfUse()
    {
        return $this->intensityOfUse;
    }

    /**
     * Set the irhp permit application
     *
     * @param \Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication $irhpPermitApplication entity being set as the value
     *
     * @return IrhpCandidatePermit
     */
    public function setIrhpPermitApplication($irhpPermitApplication)
    {
        $this->irhpPermitApplication = $irhpPermitApplication;

        return $this;
    }

    /**
     * Get the irhp permit application
     *
     * @return \Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication
     */
    public function getIrhpPermitApplication()
    {
        return $this->irhpPermitApplication;
    }

    /**
     * Set the irhp permit range
     *
     * @param \Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange $irhpPermitRange entity being set as the value
     *
     * @return IrhpCandidatePermit
     */
    public function setIrhpPermitRange($irhpPermitRange)
    {
        $this->irhpPermitRange = $irhpPermitRange;

        return $this;
    }

    /**
     * Get the irhp permit range
     *
     * @return \Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange
     */
    public function getIrhpPermitRange()
    {
        return $this->irhpPermitRange;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy entity being set as the value
     *
     * @return IrhpCandidatePermit
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
     * @return IrhpCandidatePermit
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
     * Set the randomized score
     *
     * @param float $randomizedScore new value being set
     *
     * @return IrhpCandidatePermit
     */
    public function setRandomizedScore($randomizedScore)
    {
        $this->randomizedScore = $randomizedScore;

        return $this;
    }

    /**
     * Get the randomized score
     *
     * @return float
     */
    public function getRandomizedScore()
    {
        return $this->randomizedScore;
    }

    /**
     * Set the successful
     *
     * @param boolean $successful new value being set
     *
     * @return IrhpCandidatePermit
     */
    public function setSuccessful($successful)
    {
        $this->successful = $successful;

        return $this;
    }

    /**
     * Get the successful
     *
     * @return boolean
     */
    public function getSuccessful()
    {
        return $this->successful;
    }

    /**
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return IrhpCandidatePermit
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
     * Set the irhp permit
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpPermits collection being set as the value
     *
     * @return IrhpCandidatePermit
     */
    public function setIrhpPermits($irhpPermits)
    {
        $this->irhpPermits = $irhpPermits;

        return $this;
    }

    /**
     * Get the irhp permits
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getIrhpPermits()
    {
        return $this->irhpPermits;
    }

    /**
     * Add a irhp permits
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpPermits collection being added
     *
     * @return IrhpCandidatePermit
     */
    public function addIrhpPermits($irhpPermits)
    {
        if ($irhpPermits instanceof ArrayCollection) {
            $this->irhpPermits = new ArrayCollection(
                array_merge(
                    $this->irhpPermits->toArray(),
                    $irhpPermits->toArray()
                )
            );
        } elseif (!$this->irhpPermits->contains($irhpPermits)) {
            $this->irhpPermits->add($irhpPermits);
        }

        return $this;
    }

    /**
     * Remove a irhp permits
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpPermits collection being removed
     *
     * @return IrhpCandidatePermit
     */
    public function removeIrhpPermits($irhpPermits)
    {
        if ($this->irhpPermits->contains($irhpPermits)) {
            $this->irhpPermits->removeElement($irhpPermits);
        }

        return $this;
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
                if ($this->$property instanceof Collection) {
                    $this->$property = new ArrayCollection(array());
                } else {
                    $this->$property = null;
                }
            }
        }
    }
}
