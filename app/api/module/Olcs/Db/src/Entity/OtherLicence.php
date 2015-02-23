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
 *        @ORM\Index(name="fk_other_licence_transport_manager_licence1_idx", columns={"transport_manager_licence_id"}),
 *        @ORM\Index(name="fk_other_licence_transport_manager_application1_idx", columns={"transport_manager_application_id"})
 *    }
 * )
 */
class OtherLicence implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\AdditionalInformation4000Field,
        Traits\ApplicationManyToOneAlt1,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\DisqualificationDateField,
        Traits\DisqualificationLength255Field,
        Traits\HolderName90Field,
        Traits\HoursPerWeekField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\LicNo18Field,
        Traits\PurchaseDateField,
        Traits\CustomVersionField;

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
