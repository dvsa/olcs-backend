<?php

namespace Dvsa\Olcs\Api\Entity\View;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use JsonSerializable;

/**
 * Bus Reg Browse View
 *
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="bus_reg_browse_view")
 */
class BusRegBrowseView implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;

    /**
     * Id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * Traffic area id
     *
     * @var string
     *
     * @ORM\Column(type="string", name="traffic_area_id")
     */
    protected $trafficAreaId;

    /**
     * Traffic area name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="traffic_area_name")
     */
    protected $trafficAreaName;

    /**
     * Name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="name")
     */
    protected $name;

    /**
     * Address
     *
     * @var string
     *
     * @ORM\Column(type="string", name="address")
     */
    protected $address;

    /**
     * Licence Number
     *
     * @var string
     *
     * @ORM\Column(type="string", name="lic_no")
     */
    protected $licNo;

    /**
     * Licence Status
     *
     * @var string
     *
     * @ORM\Column(type="string", name="lic_status")
     */
    protected $licStatus;

    /**
     * Reg No
     *
     * @var string
     *
     * @ORM\Column(type="string", name="reg_no")
     */
    protected $regNo;

    /**
     * Bus reg status
     *
     * @var string
     *
     * @ORM\Column(type="string", name="br_status")
     */
    protected $brStatus;

    /**
     * Variation Number
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="variation_no")
     */
    protected $variationNo;

    /**
     * Received date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="received_date")
     */
    protected $receivedDate;

    /**
     * Effective date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="effective_date")
     */
    protected $effectiveDate;

    /**
     * End date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="end_date")
     */
    protected $endDate;

    /**
     * Service number
     *
     * @var string
     *
     * @ORM\Column(type="string", name="service_no")
     */
    protected $serviceNo;

    /**
     * Other details
     *
     * @var string
     *
     * @ORM\Column(type="string", name="other_details")
     */
    protected $otherDetails;

    /**
     * Accepted date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="accepted_date")
     */
    protected $acceptedDate;

    /**
     * Event description
     *
     * @var string
     *
     * @ORM\Column(type="string", name="event_description")
     */
    protected $eventDescription;

    /**
     * Event registration status
     *
     * @var string
     *
     * @ORM\Column(type="string", name="event_registration_status")
     */
    protected $eventRegistrationStatus;

    /**
     * Status
     *
     * @var string
     *
     * @ORM\Column(type="string", name="status")
     */
    protected $status;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param int $id Id
     *
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get traffic area id
     *
     * @return string
     */
    public function getTrafficAreaId()
    {
        return $this->trafficAreaId;
    }

    /**
     * Set traffic area id
     *
     * @param string $trafficAreaId Traffic area id
     *
     * @return void
     */
    public function setTrafficAreaId($trafficAreaId)
    {
        $this->trafficAreaId = $trafficAreaId;
    }

    /**
     * Get traffic area name
     *
     * @return string
     */
    public function getTrafficAreaName()
    {
        return $this->trafficAreaName;
    }

    /**
     * Set traffic area name
     *
     * @param string $trafficAreaName Traffic area name
     *
     * @return void
     */
    public function setTrafficAreaName($trafficAreaName)
    {
        $this->trafficAreaName = $trafficAreaName;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name Name
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set address
     *
     * @param string $address Address
     *
     * @return void
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * Get lic no
     *
     * @return string
     */
    public function getLicNo()
    {
        return $this->licNo;
    }

    /**
     * Set lic no
     *
     * @param string $licNo Lic no
     *
     * @return void
     */
    public function setLicNo($licNo)
    {
        $this->licNo = $licNo;
    }

    /**
     * Get lic status
     *
     * @return string
     */
    public function getLicStatus()
    {
        return $this->licStatus;
    }

    /**
     * Set lic status
     *
     * @param string $licStatus Lic status
     *
     * @return void
     */
    public function setLicStatus($licStatus)
    {
        $this->licStatus = $licStatus;
    }

    /**
     * Get reg no
     *
     * @return string
     */
    public function getRegNo()
    {
        return $this->regNo;
    }

    /**
     * Set reg no
     *
     * @param string $regNo Reg no
     *
     * @return void
     */
    public function setRegNo($regNo)
    {
        $this->regNo = $regNo;
    }

    /**
     * Get Bus Reg status
     *
     * @return string
     */
    public function getBrStatus()
    {
        return $this->brStatus;
    }

    /**
     * Set Bus Reg status
     *
     * @param string $brStatus Bus Reg status
     *
     * @return void
     */
    public function setBrStatus($brStatus)
    {
        $this->brStatus = $brStatus;
    }

    /**
     * Get variation no
     *
     * @return int
     */
    public function getVariationNo()
    {
        return $this->variationNo;
    }

    /**
     * Set variation no
     *
     * @param string $variationNo Variation no
     *
     * @return void
     */
    public function setVariationNo($variationNo)
    {
        $this->variationNo = $variationNo;
    }

    /**
     * Get received date
     *
     * @return \DateTime
     */
    public function getReceivedDate()
    {
        return $this->receivedDate;
    }

    /**
     * Set received date
     *
     * @param \DateTime $receivedDate Received date
     *
     * @return void
     */
    public function setReceivedDate($receivedDate)
    {
        $this->receivedDate = $receivedDate;
    }

    /**
     * Get effective date
     *
     * @return \DateTime
     */
    public function getEffectiveDate()
    {
        return $this->effectiveDate;
    }

    /**
     * Set effective date
     *
     * @param \DateTime $effectiveDate Effective date
     *
     * @return void
     */
    public function setEffectiveDate($effectiveDate)
    {
        $this->effectiveDate = $effectiveDate;
    }

    /**
     * Get end date
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set end date
     *
     * @param \DateTime $endDate End date
     *
     * @return void
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * Get service no
     *
     * @return string
     */
    public function getServiceNo()
    {
        return $this->serviceNo;
    }

    /**
     * Set service no
     *
     * @param string $serviceNo Service no
     *
     * @return void
     */
    public function setServiceNo($serviceNo)
    {
        $this->serviceNo = $serviceNo;
    }

    /**
     * Get other details
     *
     * @return string
     */
    public function getOtherDetails()
    {
        return $this->otherDetails;
    }

    /**
     * Set other details
     *
     * @param string $otherDetails Other details
     *
     * @return void
     */
    public function setOtherDetails($otherDetails)
    {
        $this->otherDetails = $otherDetails;
    }

    /**
     * Get accepted date
     *
     * @return \DateTime
     */
    public function getAcceptedDate()
    {
        return $this->acceptedDate;
    }

    /**
     * Set accepted date
     *
     * @param \DateTime $acceptedDate Accepted date
     *
     * @return void
     */
    public function setAcceptedDate($acceptedDate)
    {
        $this->acceptedDate = $acceptedDate;
    }

    /**
     * Get event description
     *
     * @return string
     */
    public function getEventDescription()
    {
        return $this->eventDescription;
    }

    /**
     * Set event description
     *
     * @param string $eventDescription Event description
     *
     * @return void
     */
    public function setEventDescription($eventDescription)
    {
        $this->eventDescription = $eventDescription;
    }

    /**
     * Get event registration status
     *
     * @return string
     */
    public function getEventRegistrationStatus()
    {
        return $this->eventRegistrationStatus;
    }

    /**
     * Set event registration status
     *
     * @param string $eventRegistrationStatus Event registration status
     *
     * @return void
     */
    public function setEventRegistrationStatus($eventRegistrationStatus)
    {
        $this->eventRegistrationStatus = $eventRegistrationStatus;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param string $status Status
     *
     * @return void
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }
}
