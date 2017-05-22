<?php

namespace Dvsa\Olcs\Api\Entity\OperatingCentre;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * OperatingCentre Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="operating_centre",
 *    indexes={
 *        @ORM\Index(name="ix_operating_centre_address_id", columns={"address_id"}),
 *        @ORM\Index(name="ix_operating_centre_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_operating_centre_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_operating_centre_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
abstract class AbstractOperatingCentre implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;

    /**
     * Address
     *
     * @var \Dvsa\Olcs\Api\Entity\ContactDetails\Address
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\ContactDetails\Address", fetch="LAZY")
     * @ORM\JoinColumn(name="address_id", referencedColumnName="id", nullable=true)
     */
    protected $address;

    /**
     * Complaint
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Cases\Complaint",
     *     mappedBy="operatingCentres",
     *     fetch="LAZY"
     * )
     */
    protected $complaints;

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
     * Olbs key
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="olbs_key", nullable=true)
     */
    protected $olbsKey;

    /**
     * Opposition
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Opposition\Opposition",
     *     mappedBy="operatingCentres",
     *     fetch="LAZY"
     * )
     */
    protected $oppositions;

    /**
     * Transport manager application
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication",
     *     mappedBy="operatingCentres",
     *     fetch="LAZY"
     * )
     */
    protected $transportManagerApplications;

    /**
     * Transport manager licence
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Tm\TransportManagerLicence",
     *     mappedBy="operatingCentres",
     *     fetch="LAZY"
     * )
     */
    protected $transportManagerLicences;

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
     * Application
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre",
     *     mappedBy="operatingCentre"
     * )
     */
    protected $applications;

    /**
     * Condition undertaking
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Cases\ConditionUndertaking",
     *     mappedBy="operatingCentre"
     * )
     */
    protected $conditionUndertakings;

    /**
     * Ad document
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Dvsa\Olcs\Api\Entity\Doc\Document", mappedBy="operatingCentre")
     */
    protected $adDocuments;

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
        $this->transportManagerLicences = new ArrayCollection();
        $this->transportManagerApplications = new ArrayCollection();
        $this->complaints = new ArrayCollection();
        $this->oppositions = new ArrayCollection();
        $this->applications = new ArrayCollection();
        $this->conditionUndertakings = new ArrayCollection();
        $this->adDocuments = new ArrayCollection();
    }

    /**
     * Set the address
     *
     * @param \Dvsa\Olcs\Api\Entity\ContactDetails\Address $address entity being set as the value
     *
     * @return OperatingCentre
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get the address
     *
     * @return \Dvsa\Olcs\Api\Entity\ContactDetails\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set the complaint
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $complaints collection being set as the value
     *
     * @return OperatingCentre
     */
    public function setComplaints($complaints)
    {
        $this->complaints = $complaints;

        return $this;
    }

    /**
     * Get the complaints
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getComplaints()
    {
        return $this->complaints;
    }

    /**
     * Add a complaints
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $complaints collection being added
     *
     * @return OperatingCentre
     */
    public function addComplaints($complaints)
    {
        if ($complaints instanceof ArrayCollection) {
            $this->complaints = new ArrayCollection(
                array_merge(
                    $this->complaints->toArray(),
                    $complaints->toArray()
                )
            );
        } elseif (!$this->complaints->contains($complaints)) {
            $this->complaints->add($complaints);
        }

        return $this;
    }

    /**
     * Remove a complaints
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $complaints collection being removed
     *
     * @return OperatingCentre
     */
    public function removeComplaints($complaints)
    {
        if ($this->complaints->contains($complaints)) {
            $this->complaints->removeElement($complaints);
        }

        return $this;
    }

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy entity being set as the value
     *
     * @return OperatingCentre
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
     * @return OperatingCentre
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
     * Set the id
     *
     * @param int $id new value being set
     *
     * @return OperatingCentre
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
     * @return OperatingCentre
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
     * @return OperatingCentre
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
     * Set the olbs key
     *
     * @param int $olbsKey new value being set
     *
     * @return OperatingCentre
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
     * Set the opposition
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $oppositions collection being set as the value
     *
     * @return OperatingCentre
     */
    public function setOppositions($oppositions)
    {
        $this->oppositions = $oppositions;

        return $this;
    }

    /**
     * Get the oppositions
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getOppositions()
    {
        return $this->oppositions;
    }

    /**
     * Add a oppositions
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $oppositions collection being added
     *
     * @return OperatingCentre
     */
    public function addOppositions($oppositions)
    {
        if ($oppositions instanceof ArrayCollection) {
            $this->oppositions = new ArrayCollection(
                array_merge(
                    $this->oppositions->toArray(),
                    $oppositions->toArray()
                )
            );
        } elseif (!$this->oppositions->contains($oppositions)) {
            $this->oppositions->add($oppositions);
        }

        return $this;
    }

    /**
     * Remove a oppositions
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $oppositions collection being removed
     *
     * @return OperatingCentre
     */
    public function removeOppositions($oppositions)
    {
        if ($this->oppositions->contains($oppositions)) {
            $this->oppositions->removeElement($oppositions);
        }

        return $this;
    }

    /**
     * Set the transport manager application
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $transportManagerApplications collection being set as the value
     *
     * @return OperatingCentre
     */
    public function setTransportManagerApplications($transportManagerApplications)
    {
        $this->transportManagerApplications = $transportManagerApplications;

        return $this;
    }

    /**
     * Get the transport manager applications
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTransportManagerApplications()
    {
        return $this->transportManagerApplications;
    }

    /**
     * Add a transport manager applications
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $transportManagerApplications collection being added
     *
     * @return OperatingCentre
     */
    public function addTransportManagerApplications($transportManagerApplications)
    {
        if ($transportManagerApplications instanceof ArrayCollection) {
            $this->transportManagerApplications = new ArrayCollection(
                array_merge(
                    $this->transportManagerApplications->toArray(),
                    $transportManagerApplications->toArray()
                )
            );
        } elseif (!$this->transportManagerApplications->contains($transportManagerApplications)) {
            $this->transportManagerApplications->add($transportManagerApplications);
        }

        return $this;
    }

    /**
     * Remove a transport manager applications
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $transportManagerApplications collection being removed
     *
     * @return OperatingCentre
     */
    public function removeTransportManagerApplications($transportManagerApplications)
    {
        if ($this->transportManagerApplications->contains($transportManagerApplications)) {
            $this->transportManagerApplications->removeElement($transportManagerApplications);
        }

        return $this;
    }

    /**
     * Set the transport manager licence
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $transportManagerLicences collection being set as the value
     *
     * @return OperatingCentre
     */
    public function setTransportManagerLicences($transportManagerLicences)
    {
        $this->transportManagerLicences = $transportManagerLicences;

        return $this;
    }

    /**
     * Get the transport manager licences
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTransportManagerLicences()
    {
        return $this->transportManagerLicences;
    }

    /**
     * Add a transport manager licences
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $transportManagerLicences collection being added
     *
     * @return OperatingCentre
     */
    public function addTransportManagerLicences($transportManagerLicences)
    {
        if ($transportManagerLicences instanceof ArrayCollection) {
            $this->transportManagerLicences = new ArrayCollection(
                array_merge(
                    $this->transportManagerLicences->toArray(),
                    $transportManagerLicences->toArray()
                )
            );
        } elseif (!$this->transportManagerLicences->contains($transportManagerLicences)) {
            $this->transportManagerLicences->add($transportManagerLicences);
        }

        return $this;
    }

    /**
     * Remove a transport manager licences
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $transportManagerLicences collection being removed
     *
     * @return OperatingCentre
     */
    public function removeTransportManagerLicences($transportManagerLicences)
    {
        if ($this->transportManagerLicences->contains($transportManagerLicences)) {
            $this->transportManagerLicences->removeElement($transportManagerLicences);
        }

        return $this;
    }

    /**
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return OperatingCentre
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
     * Set the application
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $applications collection being set as the value
     *
     * @return OperatingCentre
     */
    public function setApplications($applications)
    {
        $this->applications = $applications;

        return $this;
    }

    /**
     * Get the applications
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * Add a applications
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $applications collection being added
     *
     * @return OperatingCentre
     */
    public function addApplications($applications)
    {
        if ($applications instanceof ArrayCollection) {
            $this->applications = new ArrayCollection(
                array_merge(
                    $this->applications->toArray(),
                    $applications->toArray()
                )
            );
        } elseif (!$this->applications->contains($applications)) {
            $this->applications->add($applications);
        }

        return $this;
    }

    /**
     * Remove a applications
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $applications collection being removed
     *
     * @return OperatingCentre
     */
    public function removeApplications($applications)
    {
        if ($this->applications->contains($applications)) {
            $this->applications->removeElement($applications);
        }

        return $this;
    }

    /**
     * Set the condition undertaking
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $conditionUndertakings collection being set as the value
     *
     * @return OperatingCentre
     */
    public function setConditionUndertakings($conditionUndertakings)
    {
        $this->conditionUndertakings = $conditionUndertakings;

        return $this;
    }

    /**
     * Get the condition undertakings
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getConditionUndertakings()
    {
        return $this->conditionUndertakings;
    }

    /**
     * Add a condition undertakings
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $conditionUndertakings collection being added
     *
     * @return OperatingCentre
     */
    public function addConditionUndertakings($conditionUndertakings)
    {
        if ($conditionUndertakings instanceof ArrayCollection) {
            $this->conditionUndertakings = new ArrayCollection(
                array_merge(
                    $this->conditionUndertakings->toArray(),
                    $conditionUndertakings->toArray()
                )
            );
        } elseif (!$this->conditionUndertakings->contains($conditionUndertakings)) {
            $this->conditionUndertakings->add($conditionUndertakings);
        }

        return $this;
    }

    /**
     * Remove a condition undertakings
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $conditionUndertakings collection being removed
     *
     * @return OperatingCentre
     */
    public function removeConditionUndertakings($conditionUndertakings)
    {
        if ($this->conditionUndertakings->contains($conditionUndertakings)) {
            $this->conditionUndertakings->removeElement($conditionUndertakings);
        }

        return $this;
    }

    /**
     * Set the ad document
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $adDocuments collection being set as the value
     *
     * @return OperatingCentre
     */
    public function setAdDocuments($adDocuments)
    {
        $this->adDocuments = $adDocuments;

        return $this;
    }

    /**
     * Get the ad documents
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getAdDocuments()
    {
        return $this->adDocuments;
    }

    /**
     * Add a ad documents
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $adDocuments collection being added
     *
     * @return OperatingCentre
     */
    public function addAdDocuments($adDocuments)
    {
        if ($adDocuments instanceof ArrayCollection) {
            $this->adDocuments = new ArrayCollection(
                array_merge(
                    $this->adDocuments->toArray(),
                    $adDocuments->toArray()
                )
            );
        } elseif (!$this->adDocuments->contains($adDocuments)) {
            $this->adDocuments->add($adDocuments);
        }

        return $this;
    }

    /**
     * Remove a ad documents
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $adDocuments collection being removed
     *
     * @return OperatingCentre
     */
    public function removeAdDocuments($adDocuments)
    {
        if ($this->adDocuments->contains($adDocuments)) {
            $this->adDocuments->removeElement($adDocuments);
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
