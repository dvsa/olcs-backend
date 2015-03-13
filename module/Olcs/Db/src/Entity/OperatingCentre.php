<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;

/**
 * OperatingCentre Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
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
class OperatingCentre implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\AddressManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\OlbsKeyField,
        Traits\CustomVersionField,
        Traits\ViAction1Field;

    /**
     * Opposition
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\Opposition", mappedBy="operatingCentres")
     */
    protected $oppositions;

    /**
     * Transport manager application
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\TransportManagerApplication", mappedBy="operatingCentres")
     */
    protected $transportManagerApplications;

    /**
     * Transport manager licence
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\TransportManagerLicence", mappedBy="operatingCentres")
     */
    protected $transportManagerLicences;

    /**
     * Condition undertaking
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\ConditionUndertaking", mappedBy="operatingCentre")
     */
    protected $conditionUndertakings;

    /**
     * Ad document
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\Document", mappedBy="operatingCentre")
     */
    protected $adDocuments;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->transportManagerLicences = new ArrayCollection();
        $this->oppositions = new ArrayCollection();
        $this->transportManagerApplications = new ArrayCollection();
        $this->conditionUndertakings = new ArrayCollection();
        $this->adDocuments = new ArrayCollection();
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
}
