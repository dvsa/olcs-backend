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
 *        @ORM\Index(name="IDX_B3145C7FF5B7AF75", columns={"address_id"}),
 *        @ORM\Index(name="IDX_B3145C7F65CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_B3145C7FDE12AB56", columns={"created_by"})
 *    }
 * )
 */
class OperatingCentre implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\ViAction1Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Address
     *
     * @var \Olcs\Db\Entity\Address
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Address", fetch="LAZY")
     * @ORM\JoinColumn(name="address_id", referencedColumnName="id", nullable=true)
     */
    protected $address;

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
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be changed to use doctrine colelction add/remove directly inside a loop as this
     * will save database calls when updating an entity
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
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be updated to take either an iterable or a single object and to determine if it
     * should use remove or removeElement to remove the object (use is_scalar)
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
