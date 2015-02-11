<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * TransportManagerLicence Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="transport_manager_licence",
 *    indexes={
 *        @ORM\Index(name="fk_transport_manager_licence_transport_manager1_idx", columns={"transport_manager_id"}),
 *        @ORM\Index(name="fk_transport_manager_licence_licence1_idx", columns={"licence_id"}),
 *        @ORM\Index(name="fk_transport_manager_licence_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_transport_manager_licence_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_transport_manager_licence_ref_data1_idx", columns={"tm_type"})
 *    }
 * )
 */
class TransportManagerLicence implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\AdditionalInformation4000Field,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\HoursFriField,
        Traits\HoursMonField,
        Traits\HoursSatField,
        Traits\HoursSunField,
        Traits\HoursThuField,
        Traits\HoursTueField,
        Traits\HoursWedField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\OlbsKeyField,
        Traits\TmTypeManyToOne,
        Traits\TransportManagerManyToOneAlt1,
        Traits\CustomVersionField;

    /**
     * Licence
     *
     * @var \Olcs\Db\Entity\Licence
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Licence", inversedBy="tmLicences")
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id", nullable=false)
     */
    protected $licence;

    /**
     * Operating centre
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\OperatingCentre", inversedBy="transportManagerLicences")
     * @ORM\JoinTable(name="tm_licence_oc",
     *     joinColumns={
     *         @ORM\JoinColumn(name="transport_manager_licence_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="operating_centre_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $operatingCentres;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->operatingCentres = new ArrayCollection();
    }

    /**
     * Set the licence
     *
     * @param \Olcs\Db\Entity\Licence $licence
     * @return TransportManagerLicence
     */
    public function setLicence($licence)
    {
        $this->licence = $licence;

        return $this;
    }

    /**
     * Get the licence
     *
     * @return \Olcs\Db\Entity\Licence
     */
    public function getLicence()
    {
        return $this->licence;
    }

    /**
     * Set the operating centre
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $operatingCentres
     * @return TransportManagerLicence
     */
    public function setOperatingCentres($operatingCentres)
    {
        $this->operatingCentres = $operatingCentres;

        return $this;
    }

    /**
     * Get the operating centres
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getOperatingCentres()
    {
        return $this->operatingCentres;
    }

    /**
     * Add a operating centres
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $operatingCentres
     * @return TransportManagerLicence
     */
    public function addOperatingCentres($operatingCentres)
    {
        if ($operatingCentres instanceof ArrayCollection) {
            $this->operatingCentres = new ArrayCollection(
                array_merge(
                    $this->operatingCentres->toArray(),
                    $operatingCentres->toArray()
                )
            );
        } elseif (!$this->operatingCentres->contains($operatingCentres)) {
            $this->operatingCentres->add($operatingCentres);
        }

        return $this;
    }

    /**
     * Remove a operating centres
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $operatingCentres
     * @return TransportManagerLicence
     */
    public function removeOperatingCentres($operatingCentres)
    {
        if ($this->operatingCentres->contains($operatingCentres)) {
            $this->operatingCentres->removeElement($operatingCentres);
        }

        return $this;
    }
}
