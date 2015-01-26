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
 *        @ORM\Index(name="fk_OperatingCentre_Address1_idx", columns={"address_id"}),
 *        @ORM\Index(name="fk_operating_centre_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_operating_centre_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class OperatingCentre implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField,
        Traits\ViAction1Field;

    /**
     * Address
     *
     * @var \Olcs\Db\Entity\Address
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Address")
     * @ORM\JoinColumn(name="address_id", referencedColumnName="id", nullable=true)
     */
    protected $address;

    /**
     * Opposition
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\Opposition", mappedBy="operatingCentres")
     */
    protected $oppositions;

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
        $this->oppositions = new ArrayCollection();
        $this->adDocuments = new ArrayCollection();
    }

    /**
     * Set the address
     *
     * @param \Olcs\Db\Entity\Address $address
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
     * @return \Olcs\Db\Entity\Address
     */
    public function getAddress()
    {
        return $this->address;
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
