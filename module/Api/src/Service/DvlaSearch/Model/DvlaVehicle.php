<?php

namespace Dvsa\Olcs\Api\Service\DvlaSearch\Model;

/**
 * Class DvlaVehicle
 * @package Dvsa\Olcs\Api\Service\DvlaSearch\Model
 *
 * Object representation of a vehicle returned from DVSA Vehicle Enquiry API
 * https://developer-portal.driver-vehicle-licensing.api.gov.uk/apis/vehicle-enquiry-service/v1.1.0-vehicle-enquiry-service.html#vehicle-enquiry-api
 */
class DvlaVehicle
{
    /**
     * Registration number of the vehicle
     *
     * @var string
     */
    private $registrationNumber;

    /**
     * Tax status of the vehicle
     * Enum:
     *      Not Taxed for on Road Use
     *      SORN
     *      Taxed
     *      Untaxed
     *
     * @var string|null
     */
    private $taxStatus;

    /**
     * Date of tax liability, used in calculating licence information presented to user
     *
     * @var \DateTime|null
     */
    private $taxDueDate;

    /**
     * Additional Rate of Tax End Date
     *
     * @var \DateTime|null
     */
    private $artEndDate;

    /**
     * MOT Status of the vehicle
     * Enum:
     *      No details held by DVLA
     *      No results returned
     *      Not valid
     *      Valid
     *
     * @var string|null
     */
    private $motStatus;

    /**
     * Mot Expiry Date
     *
     * @var \DateTime|null
     */
    private $motExpiryDate;

    /**
     * Vehicle make
     *
     * @var string|null
     */
    private $make;

    /**
     * Month of First DVLA Registration
     *
     * @var \DateTime|null
     */
    private $monthOfFirstDvlaRegistration;

    /**
     * Month of First Registration
     *
     * @var \DateTime|null
     */
    private $monthOfFirstRegistration;

    /**
     * Year of Manufacture
     *
     * @var integer|null
     */
    private $yearOfManufacture;

    /**
     * Engine capacity in cubic centimetres
     *
     * @var integer|null
     */
    private $engineCapacity;

    /**
     * Carbon Dioxide emissions in grams per kilometre
     *
     * @var integer|null
     */
    private $co2Emissions;

    /**
     * Fuel type (Method of Propulsion)
     *
     * @var string|null
     */
    private $fuelType;

    /**
     * True only if vehicle has been export marked
     *
     * @var boolean|null
     */
    private $markedForExport;

    /**
     * Vehicle colour
     * @var string|null
     */
    private $colour;

    /**
     * Vehicle Type Approval Category
     *
     * @var string|null
     */
    private $typeApproval;

    /**
     * Vehicle wheel plan
     *
     * @var string|null
     */
    private $wheelplan;

    /**
     * Revenue weight in kilograms
     *
     * @var integer|null
     */
    private $revenueWeight;

    /**
     * Real Driving Emissions value
     *
     * @var string|null
     */
    private $realDrivingEmissions;

    /**
     * Date of last V5C issued
     *
     * @var \DateTime|null
     */
    private $dateOfLastV5CIssued;

    /**
     * Euro Status (Dealer / Customer Provided (new vehicles))
     *
     * @var string|null
     */
    private $euroStatus;

    public function __construct(
        string $registrationNumber,
        ?string $taxStatus,
        ?\DateTime $taxDueDate,
        ?\DateTime $artEndDate,
        ?string $motStatus,
        ?\DateTime $motExpiryDate,
        ?string $make,
        ?\DateTime $monthOfFirstDvlaRegistration,
        ?\DateTime $monthOfFirstRegistration,
        ?int $yearOfManufacture,
        ?int $engineCapacity,
        ?int $co2Emissions,
        ?string $fuelType,
        ?bool $markedForExport,
        ?string $colour,
        ?string $typeApproval,
        ?string $wheelplan,
        ?int $revenueWeight,
        ?string $realDrivingEmissions,
        ?\DateTime $dateOfLastV5CIssued,
        ?string $euroStatus
    ) {
        $this->registrationNumber = $registrationNumber;
        $this->taxStatus = $taxStatus;
        $this->taxDueDate = $taxDueDate;
        $this->artEndDate = $artEndDate;
        $this->motStatus = $motStatus;
        $this->motExpiryDate = $motExpiryDate;
        $this->make = $make;
        $this->monthOfFirstDvlaRegistration = $monthOfFirstDvlaRegistration;
        $this->monthOfFirstRegistration = $monthOfFirstRegistration;
        $this->yearOfManufacture = $yearOfManufacture;
        $this->engineCapacity = $engineCapacity;
        $this->co2Emissions = $co2Emissions;
        $this->fuelType = $fuelType;
        $this->markedForExport = $markedForExport;
        $this->colour = $colour;
        $this->typeApproval = $typeApproval;
        $this->wheelplan = $wheelplan;
        $this->revenueWeight = $revenueWeight;
        $this->realDrivingEmissions = $realDrivingEmissions;
        $this->dateOfLastV5CIssued = $dateOfLastV5CIssued;
        $this->euroStatus = $euroStatus;
    }

    public function getRegistrationNumber(): string
    {
        return $this->registrationNumber;
    }

    public function getTaxStatus(): ?string
    {
        return $this->taxStatus;
    }

    public function getTaxDueDate(): ?\DateTime
    {
        return $this->taxDueDate;
    }

    public function getArtEndDate(): ?\DateTime
    {
        return $this->artEndDate;
    }

    public function getMotStatus(): ?string
    {
        return $this->motStatus;
    }

    public function getMotExpiryDate(): ?\DateTime
    {
        return $this->motExpiryDate;
    }

    public function getMake(): ?string
    {
        return $this->make;
    }

    public function getMonthOfFirstDvlaRegistration(): ?\DateTime
    {
        return $this->monthOfFirstDvlaRegistration;
    }

    public function getMonthOfFirstRegistration(): ?\DateTime
    {
        return $this->monthOfFirstRegistration;
    }

    public function getYearOfManufacture(): ?int
    {
        return $this->yearOfManufacture;
    }

    public function getEngineCapacity(): ?int
    {
        return $this->engineCapacity;
    }

    public function getCo2Emissions(): ?int
    {
        return $this->co2Emissions;
    }

    public function getFuelType(): ?string
    {
        return $this->fuelType;
    }

    public function getMarkedForExport(): ?bool
    {
        return $this->markedForExport;
    }

    public function getColour(): ?string
    {
        return $this->colour;
    }

    public function getTypeApproval(): ?string
    {
        return $this->typeApproval;
    }

    public function getWheelplan(): ?string
    {
        return $this->wheelplan;
    }

    public function getRevenueWeight(): ?int
    {
        return $this->revenueWeight;
    }

    public function getRealDrivingEmissions(): ?string
    {
        return $this->realDrivingEmissions;
    }

    public function getDateOfLastV5CIssued(): ?\DateTime
    {
        return $this->dateOfLastV5CIssued;
    }

    public function getEuroStatus(): ?string
    {
        return $this->euroStatus;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'registrationNumber' => $this->getRegistrationNumber(),
            'taxStatus' => $this->getTaxStatus(),
            'taxDueDate' => $this->getTaxDueDate(),
            'artEndDate' => $this->getArtEndDate(),
            'motStatus' => $this->getMotStatus(),
            'motExpiryDate' => $this->getMotExpiryDate(),
            'make' => $this->getMake(),
            'monthOfFirstDvlaRegistration' => $this->getMonthOfFirstDvlaRegistration(),
            'monthOfFirstRegistration' => $this->getMonthOfFirstRegistration(),
            'yearOfManufacture' => $this->getYearOfManufacture(),
            'engineCapacity' => $this->getEngineCapacity(),
            'co2Emissions' => $this->getCo2Emissions(),
            'fuelType' => $this->getFuelType(),
            'markedForExport' => $this->getMarkedForExport(),
            'colour' => $this->getColour(),
            'typeApproval' => $this->getTypeApproval(),
            'wheelplan' => $this->getWheelplan(),
            'revenueWeight' => $this->getRevenueWeight(),
            'realDrivingEmissions' => $this->getRealDrivingEmissions(),
            'dateOfLastV5CIssued' => $this->getDateOfLastV5CIssued(),
            'euroStatus' => $this->getEuroStatus()
        ];
    }
}
