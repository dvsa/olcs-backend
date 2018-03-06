<?php

namespace Dvsa\Olcs\Api\Entity;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * EcmtPermitsApplication Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="ecmt_permits_application",
 *    indexes={
 *        @ORM\Index(name="ecmt_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ecmt_payment_status_id", columns={"payment_status_id"}),
 *        @ORM\Index(name="ecmt_application_status_id", columns={"application_status_id"}),
 *        @ORM\Index(name="ecmt_ecmt_permits_application_created_by", columns={"created_by"})
 *    }
 * )
 */
abstract class AbstractEcmtPermitsApplication implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;

    /**
     * Identifier - Application id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="application_id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $applicationId;

    /**
     * Application status id
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="application_status_id", nullable=false)
     */
    protected $applicationStatusId;

    /**
     * Created by
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="created_by", nullable=false)
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
     * Last modified by
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="last_modified_by", nullable=true)
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
     * Licence id
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="licence_id", nullable=false)
     */
    protected $licenceId;

    /**
     * No of permits
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="no_of_permits", nullable=true)
     */
    protected $noOfPermits;

    /**
     * Payment status id
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="payment_status_id", nullable=false)
     */
    protected $paymentStatusId;

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
     * Set the application id
     *
     * @param int $applicationId new value being set
     *
     * @return EcmtPermitsApplication
     */
    public function setApplicationId($applicationId)
    {
        $this->applicationId = $applicationId;

        return $this;
    }

    /**
     * Get the application id
     *
     * @return int
     */
    public function getApplicationId()
    {
        return $this->applicationId;
    }

    /**
     * Set the application status id
     *
     * @param int $applicationStatusId new value being set
     *
     * @return EcmtPermitsApplication
     */
    public function setApplicationStatusId($applicationStatusId)
    {
        $this->applicationStatusId = $applicationStatusId;

        return $this;
    }

    /**
     * Get the application status id
     *
     * @return int
     */
    public function getApplicationStatusId()
    {
        return $this->applicationStatusId;
    }

    /**
     * Set the created by
     *
     * @param int $createdBy new value being set
     *
     * @return EcmtPermitsApplication
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get the created by
     *
     * @return int
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
     * @return EcmtPermitsApplication
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
     * Set the last modified by
     *
     * @param int $lastModifiedBy new value being set
     *
     * @return EcmtPermitsApplication
     */
    public function setLastModifiedBy($lastModifiedBy)
    {
        $this->lastModifiedBy = $lastModifiedBy;

        return $this;
    }

    /**
     * Get the last modified by
     *
     * @return int
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
     * @return EcmtPermitsApplication
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
     * Set the licence id
     *
     * @param int $licenceId new value being set
     *
     * @return EcmtPermitsApplication
     */
    public function setLicenceId($licenceId)
    {
        $this->licenceId = $licenceId;

        return $this;
    }

    /**
     * Get the licence id
     *
     * @return int
     */
    public function getLicenceId()
    {
        return $this->licenceId;
    }

    /**
     * Set the no of permits
     *
     * @param int $noOfPermits new value being set
     *
     * @return EcmtPermitsApplication
     */
    public function setNoOfPermits($noOfPermits)
    {
        $this->noOfPermits = $noOfPermits;

        return $this;
    }

    /**
     * Get the no of permits
     *
     * @return int
     */
    public function getNoOfPermits()
    {
        return $this->noOfPermits;
    }

    /**
     * Set the payment status id
     *
     * @param int $paymentStatusId new value being set
     *
     * @return EcmtPermitsApplication
     */
    public function setPaymentStatusId($paymentStatusId)
    {
        $this->paymentStatusId = $paymentStatusId;

        return $this;
    }

    /**
     * Get the payment status id
     *
     * @return int
     */
    public function getPaymentStatusId()
    {
        return $this->paymentStatusId;
    }

    /**
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return EcmtPermitsApplication
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
