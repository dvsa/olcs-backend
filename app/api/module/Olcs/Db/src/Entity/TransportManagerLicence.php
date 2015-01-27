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
     * Tm licence oc
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\TmLicenceOc", mappedBy="transportManagerLicence")
     */
    protected $tmLicenceOcs;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->tmLicenceOcs = new ArrayCollection();
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
     * Set the tm licence oc
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $tmLicenceOcs
     * @return TransportManagerLicence
     */
    public function setTmLicenceOcs($tmLicenceOcs)
    {
        $this->tmLicenceOcs = $tmLicenceOcs;

        return $this;
    }

    /**
     * Get the tm licence ocs
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTmLicenceOcs()
    {
        return $this->tmLicenceOcs;
    }

    /**
     * Add a tm licence ocs
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $tmLicenceOcs
     * @return TransportManagerLicence
     */
    public function addTmLicenceOcs($tmLicenceOcs)
    {
        if ($tmLicenceOcs instanceof ArrayCollection) {
            $this->tmLicenceOcs = new ArrayCollection(
                array_merge(
                    $this->tmLicenceOcs->toArray(),
                    $tmLicenceOcs->toArray()
                )
            );
        } elseif (!$this->tmLicenceOcs->contains($tmLicenceOcs)) {
            $this->tmLicenceOcs->add($tmLicenceOcs);
        }

        return $this;
    }

    /**
     * Remove a tm licence ocs
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $tmLicenceOcs
     * @return TransportManagerLicence
     */
    public function removeTmLicenceOcs($tmLicenceOcs)
    {
        if ($this->tmLicenceOcs->contains($tmLicenceOcs)) {
            $this->tmLicenceOcs->removeElement($tmLicenceOcs);
        }

        return $this;
    }
}
