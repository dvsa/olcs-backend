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
 *        @ORM\Index(name="ix_other_licence_application_id", columns={"application_id"}),
 *        @ORM\Index(name="ix_other_licence_previous_licence_type", columns={"previous_licence_type"}),
 *        @ORM\Index(name="ix_other_licence_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_other_licence_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_other_licence_transport_manager_id", columns={"transport_manager_id"}),
 *        @ORM\Index(name="ix_other_licence_transport_manager_application_id", columns={"transport_manager_application_id"}),
 *        @ORM\Index(name="fk_other_licence_transport_manager_licence1_idx", columns={"transport_manager_licence_id"}),
 *        @ORM\Index(name="fk_other_licence_ref_data1_idx", columns={"role"})
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
        Traits\PurchaseDateField,
        Traits\CustomVersionField;

    /**
     * Hours per week
     *
     * @var string
     *
     * @ORM\Column(type="string", name="hours_per_week", length=80, nullable=true)
     */
    protected $hoursPerWeek;

    /**
     * Operating centres
     *
     * @var string
     *
     * @ORM\Column(type="string", name="operating_centres", length=255, nullable=true)
     */
    protected $operatingCentres;

    /**
     * Previous licence type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="previous_licence_type", referencedColumnName="id", nullable=true)
     */
    protected $previousLicenceType;

    /**
     * Role
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="role", referencedColumnName="id", nullable=true)
     */
    protected $role;

    /**
     * Total auth vehicles
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="total_auth_vehicles", nullable=true)
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
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TransportManagerApplication", inversedBy="otherLicences")
     * @ORM\JoinColumn(name="transport_manager_application_id", referencedColumnName="id", nullable=true)
     */
    protected $transportManagerApplication;

    /**
     * Transport manager licence
     *
     * @var \Olcs\Db\Entity\TransportManagerLicence
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TransportManagerLicence", inversedBy="otherLicences")
     * @ORM\JoinColumn(name="transport_manager_licence_id", referencedColumnName="id", nullable=true)
     */
    protected $transportManagerLicence;

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
     * @param string $hoursPerWeek
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
     * @return string
     */
    public function getHoursPerWeek()
    {
        return $this->hoursPerWeek;
    }

    /**
     * Set the operating centres
     *
     * @param string $operatingCentres
     * @return OtherLicence
     */
    public function setOperatingCentres($operatingCentres)
    {
        $this->operatingCentres = $operatingCentres;

        return $this;
    }

    /**
     * Get the operating centres
     *
     * @return string
     */
    public function getOperatingCentres()
    {
        return $this->operatingCentres;
    }

    /**
     * Set the previous licence type
     *
     * @param \Olcs\Db\Entity\RefData $previousLicenceType
     * @return OtherLicence
     */
    public function setPreviousLicenceType($previousLicenceType)
    {
        $this->previousLicenceType = $previousLicenceType;

        return $this;
    }

    /**
     * Get the previous licence type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getPreviousLicenceType()
    {
        return $this->previousLicenceType;
    }

    /**
     * Set the role
     *
     * @param \Olcs\Db\Entity\RefData $role
     * @return OtherLicence
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get the role
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getRole()
    {
        return $this->role;
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
     * Set the transport manager licence
     *
     * @param \Olcs\Db\Entity\TransportManagerLicence $transportManagerLicence
     * @return OtherLicence
     */
    public function setTransportManagerLicence($transportManagerLicence)
    {
        $this->transportManagerLicence = $transportManagerLicence;

        return $this;
    }

    /**
     * Get the transport manager licence
     *
     * @return \Olcs\Db\Entity\TransportManagerLicence
     */
    public function getTransportManagerLicence()
    {
        return $this->transportManagerLicence;
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
