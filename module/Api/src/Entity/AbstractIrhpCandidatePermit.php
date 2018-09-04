<?php

namespace Dvsa\Olcs\Api\Entity;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Doctrine\ORM\Mapping as ORM;
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
     * @var string
     *
     * @ORM\Column(type="string", name="application_score", length=45, nullable=true)
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
     * @var string
     *
     * @ORM\Column(type="string", name="intensity_of_use", length=45, nullable=true)
     */
    protected $intensityOfUse;

    /**
     * Irhp permit application
     *
     * @var \Dvsa\Olcs\Api\Entity\IrhpPermitApplication
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\IrhpPermitApplication", fetch="LAZY")
     * @ORM\JoinColumn(name="irhp_permit_application_id", referencedColumnName="id", nullable=false)
     */
    protected $irhpPermitApplication;

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
     * @var string
     *
     * @ORM\Column(type="string", name="randomized_score", length=45, nullable=true)
     */
    protected $randomizedScore;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="version", nullable=true)
     * @ORM\Version
     */
    protected $version;

    /**
     * Set the application score
     *
     * @param string $applicationScore new value being set
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
     * @return string
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
     * @param string $intensityOfUse new value being set
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
     * @return string
     */
    public function getIntensityOfUse()
    {
        return $this->intensityOfUse;
    }

    /**
     * Set the irhp permit application
     *
     * @param \Dvsa\Olcs\Api\Entity\IrhpPermitApplication $irhpPermitApplication entity being set as the value
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
     * @return \Dvsa\Olcs\Api\Entity\IrhpPermitApplication
     */
    public function getIrhpPermitApplication()
    {
        return $this->irhpPermitApplication;
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
     * @param string $randomizedScore new value being set
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
     * @return string
     */
    public function getRandomizedScore()
    {
        return $this->randomizedScore;
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
