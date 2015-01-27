<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * OtherLicence Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="other_licence",
 *    indexes={
 *        @ORM\Index(name="fk_previous_licence_application1_idx", columns={"application_id"}),
 *        @ORM\Index(name="fk_other_licence_ref_data1_idx", columns={"previous_licence_type"}),
 *        @ORM\Index(name="fk_other_licence_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_other_licence_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_other_licence_transport_manager1_idx", columns={"transport_manager_id"}),
 *        @ORM\Index(name="fk_other_licence_transport_manager_application1_idx", columns={"transport_manager_application_id"})
 *    }
 * )
 */
class OtherLicence implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\AdditionalInformation4000Field,
        Traits\ApplicationManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\DisqualificationDateField,
        Traits\DisqualificationLength255Field,
        Traits\HolderName90Field,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\LicNo18Field,
        Traits\PreviousLicenceTypeManyToOne,
        Traits\PurchaseDateField,
        Traits\CustomVersionField;

    /**
     * Hours per week
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="hours_per_week", nullable=true)
     */
    protected $hoursPerWeek;

    /**
     * Total auth vehicles
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="total_auth_vehicles", nullable=true)
     */
    protected $totalAuthVehicles;

    /**
     * Transport manager
     *
     * @var \Olcs\Db\Entity\TransportManager
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TransportManager", inversedBy="otherLicences")
     * @ORM\JoinColumn(name="transport_manager_id", referencedColumnName="id", nullable=true)
     */
    protected $transportManager;

    /**
     * Transport manager application
     *
     * @var \Olcs\Db\Entity\TransportManagerApplication
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TransportManagerApplication")
     * @ORM\JoinColumn(name="transport_manager_application_id", referencedColumnName="id", nullable=true)
     */
    protected $transportManagerApplication;

    /**
     * Will surrender
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="will_surrender", nullable=true)
     */
    protected $willSurrender;

    /**
     * Set the hours per week
     *
     * @param int $hoursPerWeek
     * @return OtherLicence
     */
    public function setHoursPerWeek($hoursPerWeek)
    {
        $this->hoursPerWeek = $hoursPerWeek;

        return $this;
    }

    /**
     * Get the hours per week
     *
     * @return int
     */
    public function getHoursPerWeek()
    {
        return $this->hoursPerWeek;
    }

    /**
     * Set the total auth vehicles
     *
     * @param int $totalAuthVehicles
     * @return OtherLicence
     */
    public function setTotalAuthVehicles($totalAuthVehicles)
    {
        $this->totalAuthVehicles = $totalAuthVehicles;

        return $this;
    }

    /**
     * Get the total auth vehicles
     *
     * @return int
     */
    public function getTotalAuthVehicles()
    {
        return $this->totalAuthVehicles;
    }

    /**
     * Set the transport manager
     *
     * @param \Olcs\Db\Entity\TransportManager $transportManager
     * @return OtherLicence
     */
    public function setTransportManager($transportManager)
    {
        $this->transportManager = $transportManager;

        return $this;
    }

    /**
     * Get the transport manager
     *
     * @return \Olcs\Db\Entity\TransportManager
     */
    public function getTransportManager()
    {
        return $this->transportManager;
    }

    /**
     * Set the transport manager application
     *
     * @param \Olcs\Db\Entity\TransportManagerApplication $transportManagerApplication
     * @return OtherLicence
     */
    public function setTransportManagerApplication($transportManagerApplication)
    {
        $this->transportManagerApplication = $transportManagerApplication;

        return $this;
    }

    /**
     * Get the transport manager application
     *
     * @return \Olcs\Db\Entity\TransportManagerApplication
     */
    public function getTransportManagerApplication()
    {
        return $this->transportManagerApplication;
    }

    /**
     * Set the will surrender
     *
     * @param boolean $willSurrender
     * @return OtherLicence
     */
    public function setWillSurrender($willSurrender)
    {
        $this->willSurrender = $willSurrender;

        return $this;
    }

    /**
     * Get the will surrender
     *
     * @return boolean
     */
    public function getWillSurrender()
    {
        return $this->willSurrender;
    }
}
