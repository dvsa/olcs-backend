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
    public function __construct(
        /**
         * Registration number of the vehicle
         */
        private string $registrationNumber,
        /**
         * Tax status of the vehicle
         * Enum:
         *      Not Taxed for on Road Use
         *      SORN
         *      Taxed
         *      Untaxed
         */
        private ?string $taxStatus,
        /**
         * Date of tax liability, used in calculating licence information presented to user
         */
        private ?\DateTime $taxDueDate,
        /**
         * Additional Rate of Tax End Date
         */
        private ?\DateTime $artEndDate,
        /**
         * MOT Status of the vehicle
         * Enum:
         *      No details held by DVLA
         *      No results returned
         *      Not valid
         *      Valid
         */
        private ?string $motStatus,
        /**
         * Mot Expiry Date
         */
        private ?\DateTime $motExpiryDate,
        /**
         * Vehicle make
         */
        private ?string $make,
        /**
         * Month of First DVLA Registration
         */
        private ?\DateTime $monthOfFirstDvlaRegistration,
        /**
         * Month of First Registration
         */
        private ?\DateTime $monthOfFirstRegistration,
        /**
         * Year of Manufacture
         */
        private ?int $yearOfManufacture,
        /**
         * Engine capacity in cubic centimetres
         */
        private ?int $engineCapacity,
        /**
         * Carbon Dioxide emissions in grams per kilometre
         */
        private ?int $co2Emissions,
        /**
         * Fuel type (Method of Propulsion)
         */
        private ?string $fuelType,
        /**
         * True only if vehicle has been export marked
         */
        private ?bool $markedForExport,
        /**
         * Vehicle colour
         */
        private ?string $colour,
        /**
         * Vehicle Type Approval Category
         */
        private ?string $typeApproval,
        /**
         * Vehicle wheel plan
         */
        private ?string $wheelplan,
        /**
         * Revenue weight in kilograms
         */
        private ?int $revenueWeight,
        /**
         * Real Driving Emissions value
         */
        private ?string $realDrivingEmissions,
        /**
         * Date of last V5C issued
         */
        private ?\DateTime $dateOfLastV5CIssued,
        /**
         * Euro Status (Dealer / Customer Provided (new vehicles))
         */
        private ?string $euroStatus
    ) {
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
