<?php

namespace Dvsa\Olcs\Api\Entity\OperatingCentre;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

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
abstract class AbstractOperatingCentre
{

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
     * @ORM\ManyToMany(targetEntity="Dvsa\Olcs\Api\Entity\Opposition\Opposition", mappedBy="operatingCentres", fetch="LAZY")
     */
    protected $oppositions;

    /**
     * Transport manager application
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication", mappedBy="operatingCentres", fetch="LAZY")
     */
    protected $transportManagerApplications;

    /**
     * Transport manager licence
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Dvsa\Olcs\Api\Entity\Tm\TransportManagerLicence", mappedBy="operatingCentres", fetch="LAZY")
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
     * Vi action
     *
     * @var string
     *
     * @ORM\Column(type="string", name="vi_action", length=1, nullable=true)
     */
    protected $viAction;

    /**
     * Condition undertaking
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Dvsa\Olcs\Api\Entity\Cases\ConditionUndertaking", mappedBy="operatingCentre")
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
     * Oc complaint
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Dvsa\Olcs\Api\Entity\OperatingCentre\OcComplaint", mappedBy="operatingCentre")
     */
    protected $ocComplaints;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->initCollections();
    }

    public function initCollections()
    {
        $this->transportManagerLicences = new ArrayCollection();
        $this->oppositions = new ArrayCollection();
        $this->transportManagerApplications = new ArrayCollection();
        $this->conditionUndertakings = new ArrayCollection();
        $this->adDocuments = new ArrayCollection();
        $this->ocComplaints = new ArrayCollection();
    }

    /**
     * Set the address
     *
     * @param \Dvsa\Olcs\Api\Entity\ContactDetails\Address $address
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
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy
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
     * @param \DateTime $createdOn
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
     * @param int $id
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
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy
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
     * @param \DateTime $lastModifiedOn
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
     * @param int $olbsKey
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
     * @param \Doctrine\Common\Collections\ArrayCollection $oppositions
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
     * @param \Doctrine\Common\Collections\ArrayCollection $oppositions
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
     * @param \Doctrine\Common\Collections\ArrayCollection $oppositions
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
     * @param \Doctrine\Common\Collections\ArrayCollection $transportManagerApplications
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
     * @param \Doctrine\Common\Collections\ArrayCollection $transportManagerApplications
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
     * @param \Doctrine\Common\Collections\ArrayCollection $transportManagerApplications
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
     * @param \Doctrine\Common\Collections\ArrayCollection $transportManagerLicences
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
     * @param \Doctrine\Common\Collections\ArrayCollection $transportManagerLicences
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
     * @param \Doctrine\Common\Collections\ArrayCollection $transportManagerLicences
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
     * @param int $version
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
     * Set the vi action
     *
     * @param string $viAction
     * @return OperatingCentre
     */
    public function setViAction($viAction)
    {
        $this->viAction = $viAction;

        return $this;
    }

    /**
     * Get the vi action
     *
     * @return string
     */
    public function getViAction()
    {
        return $this->viAction;
    }

    /**
     * Set the condition undertaking
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $conditionUndertakings
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
     * @param \Doctrine\Common\Collections\ArrayCollection $conditionUndertakings
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
     * @param \Doctrine\Common\Collections\ArrayCollection $conditionUndertakings
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
     * @param \Doctrine\Common\Collections\ArrayCollection $adDocuments
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
     * @param \Doctrine\Common\Collections\ArrayCollection $adDocuments
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
     * @param \Doctrine\Common\Collections\ArrayCollection $adDocuments
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
     * Set the oc complaint
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $ocComplaints
     * @return OperatingCentre
     */
    public function setOcComplaints($ocComplaints)
    {
        $this->ocComplaints = $ocComplaints;

        return $this;
    }

    /**
     * Get the oc complaints
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getOcComplaints()
    {
        return $this->ocComplaints;
    }

    /**
     * Add a oc complaints
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $ocComplaints
     * @return OperatingCentre
     */
    public function addOcComplaints($ocComplaints)
    {
        if ($ocComplaints instanceof ArrayCollection) {
            $this->ocComplaints = new ArrayCollection(
                array_merge(
                    $this->ocComplaints->toArray(),
                    $ocComplaints->toArray()
                )
            );
        } elseif (!$this->ocComplaints->contains($ocComplaints)) {
            $this->ocComplaints->add($ocComplaints);
        }

        return $this;
    }

    /**
     * Remove a oc complaints
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $ocComplaints
     * @return OperatingCentre
     */
    public function removeOcComplaints($ocComplaints)
    {
        if ($this->ocComplaints->contains($ocComplaints)) {
            $this->ocComplaints->removeElement($ocComplaints);
        }

        return $this;
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
