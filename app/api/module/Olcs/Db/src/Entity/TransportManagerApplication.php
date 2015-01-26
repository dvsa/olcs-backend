<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * TransportManagerApplication Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="transport_manager_application",
 *    indexes={
 *        @ORM\Index(name="fk_transport_manager_application_transport_manager1_idx", columns={"transport_manager_id"}),
 *        @ORM\Index(name="fk_transport_manager_application_application1_idx", columns={"application_id"}),
 *        @ORM\Index(name="fk_transport_manager_application_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_transport_manager_application_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_transport_manager_application_ref_data1_idx", columns={"tm_type"}),
 *        @ORM\Index(name="fk_transport_manager_application_ref_data2_idx", columns={"tm_application_status"})
 *    }
 * )
 */
class TransportManagerApplication implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\Action1Field,
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
        Traits\TransportManagerManyToOneAlt1,
        Traits\CustomVersionField;

    /**
     * Application
     *
     * @var \Olcs\Db\Entity\Application
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Application", inversedBy="transportManagers")
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id", nullable=false)
     */
    protected $application;

    /**
     * Tm application status
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="tm_application_status", referencedColumnName="id", nullable=false)
     */
    protected $tmApplicationStatus;

    /**
     * Tm type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="tm_type", referencedColumnName="id", nullable=true)
     */
    protected $tmType;

    /**
     * Tm application oc
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\TmApplicationOc", mappedBy="transportManagerApplication")
     */
    protected $tmApplicationOcs;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->tmApplicationOcs = new ArrayCollection();
    }

    /**
     * Set the application
     *
     * @param \Olcs\Db\Entity\Application $application
     * @return TransportManagerApplication
     */
    public function setApplication($application)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Get the application
     *
     * @return \Olcs\Db\Entity\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Set the tm application status
     *
     * @param \Olcs\Db\Entity\RefData $tmApplicationStatus
     * @return TransportManagerApplication
     */
    public function setTmApplicationStatus($tmApplicationStatus)
    {
        $this->tmApplicationStatus = $tmApplicationStatus;

        return $this;
    }

    /**
     * Get the tm application status
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getTmApplicationStatus()
    {
        return $this->tmApplicationStatus;
    }

    /**
     * Set the tm type
     *
     * @param \Olcs\Db\Entity\RefData $tmType
     * @return TransportManagerApplication
     */
    public function setTmType($tmType)
    {
        $this->tmType = $tmType;

        return $this;
    }

    /**
     * Get the tm type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getTmType()
    {
        return $this->tmType;
    }

    /**
     * Set the tm application oc
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $tmApplicationOcs
     * @return TransportManagerApplication
     */
    public function setTmApplicationOcs($tmApplicationOcs)
    {
        $this->tmApplicationOcs = $tmApplicationOcs;

        return $this;
    }

    /**
     * Get the tm application ocs
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTmApplicationOcs()
    {
        return $this->tmApplicationOcs;
    }

    /**
     * Add a tm application ocs
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $tmApplicationOcs
     * @return TransportManagerApplication
     */
    public function addTmApplicationOcs($tmApplicationOcs)
    {
        if ($tmApplicationOcs instanceof ArrayCollection) {
            $this->tmApplicationOcs = new ArrayCollection(
                array_merge(
                    $this->tmApplicationOcs->toArray(),
                    $tmApplicationOcs->toArray()
                )
            );
        } elseif (!$this->tmApplicationOcs->contains($tmApplicationOcs)) {
            $this->tmApplicationOcs->add($tmApplicationOcs);
        }

        return $this;
    }

    /**
     * Remove a tm application ocs
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $tmApplicationOcs
     * @return TransportManagerApplication
     */
    public function removeTmApplicationOcs($tmApplicationOcs)
    {
        if ($this->tmApplicationOcs->contains($tmApplicationOcs)) {
            $this->tmApplicationOcs->removeElement($tmApplicationOcs);
        }

        return $this;
    }
}
