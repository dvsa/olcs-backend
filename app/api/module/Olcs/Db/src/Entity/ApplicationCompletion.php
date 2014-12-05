<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * ApplicationCompletion Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="application_completion",
 *    indexes={
 *        @ORM\Index(name="fk_application_completion_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_application_completion_user2_idx", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="fk_application_completion_application_id_udx", columns={"application_id"})
 *    }
 * )
 */
class ApplicationCompletion implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Addresses status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="addresses_status", nullable=true)
     */
    protected $addressesStatus;

    /**
     * Application
     *
     * @var \Olcs\Db\Entity\Application
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Application", inversedBy="applicationCompletions")
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id", nullable=false)
     */
    protected $application;

    /**
     * Business details status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="business_details_status", nullable=true)
     */
    protected $businessDetailsStatus;

    /**
     * Business type status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="business_type_status", nullable=true)
     */
    protected $businessTypeStatus;

    /**
     * Community licences status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="community_licences_status", nullable=true)
     */
    protected $communityLicencesStatus;

    /**
     * Conditions undertakings status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="conditions_undertakings_status", nullable=true)
     */
    protected $conditionsUndertakingsStatus;

    /**
     * Convictions penalties status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="convictions_penalties_status", nullable=true)
     */
    protected $convictionsPenaltiesStatus;

    /**
     * Discs status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="discs_status", nullable=true)
     */
    protected $discsStatus;

    /**
     * Financial evidence status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="financial_evidence_status", nullable=true)
     */
    protected $financialEvidenceStatus;

    /**
     * Financial history status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="financial_history_status", nullable=true)
     */
    protected $financialHistoryStatus;

    /**
     * Last section
     *
     * @var string
     *
     * @ORM\Column(type="string", name="last_section", length=255, nullable=true)
     */
    protected $lastSection;

    /**
     * Licence history status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="licence_history_status", nullable=true)
     */
    protected $licenceHistoryStatus;

    /**
     * Operating centres status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="operating_centres_status", nullable=true)
     */
    protected $operatingCentresStatus;

    /**
     * People status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="people_status", nullable=true)
     */
    protected $peopleStatus;

    /**
     * Safety status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="safety_status", nullable=true)
     */
    protected $safetyStatus;

    /**
     * Taxi phv status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="taxi_phv_status", nullable=true)
     */
    protected $taxiPhvStatus;

    /**
     * Transport managers status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="transport_managers_status", nullable=true)
     */
    protected $transportManagersStatus;

    /**
     * Type of licence status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="type_of_licence_status", nullable=true)
     */
    protected $typeOfLicenceStatus;

    /**
     * Undertakings status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="undertakings_status", nullable=true)
     */
    protected $undertakingsStatus;

    /**
     * Vehicles declarations status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="vehicles_declarations_status", nullable=true)
     */
    protected $vehiclesDeclarationsStatus;

    /**
     * Vehicles psv status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="vehicles_psv_status", nullable=true)
     */
    protected $vehiclesPsvStatus;

    /**
     * Vehicles status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="vehicles_status", nullable=true)
     */
    protected $vehiclesStatus;

    /**
     * Set the addresses status
     *
     * @param int $addressesStatus
     * @return ApplicationCompletion
     */
    public function setAddressesStatus($addressesStatus)
    {
        $this->addressesStatus = $addressesStatus;

        return $this;
    }

    /**
     * Get the addresses status
     *
     * @return int
     */
    public function getAddressesStatus()
    {
        return $this->addressesStatus;
    }

    /**
     * Set the application
     *
     * @param \Olcs\Db\Entity\Application $application
     * @return ApplicationCompletion
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
     * Set the business details status
     *
     * @param int $businessDetailsStatus
     * @return ApplicationCompletion
     */
    public function setBusinessDetailsStatus($businessDetailsStatus)
    {
        $this->businessDetailsStatus = $businessDetailsStatus;

        return $this;
    }

    /**
     * Get the business details status
     *
     * @return int
     */
    public function getBusinessDetailsStatus()
    {
        return $this->businessDetailsStatus;
    }

    /**
     * Set the business type status
     *
     * @param int $businessTypeStatus
     * @return ApplicationCompletion
     */
    public function setBusinessTypeStatus($businessTypeStatus)
    {
        $this->businessTypeStatus = $businessTypeStatus;

        return $this;
    }

    /**
     * Get the business type status
     *
     * @return int
     */
    public function getBusinessTypeStatus()
    {
        return $this->businessTypeStatus;
    }

    /**
     * Set the community licences status
     *
     * @param int $communityLicencesStatus
     * @return ApplicationCompletion
     */
    public function setCommunityLicencesStatus($communityLicencesStatus)
    {
        $this->communityLicencesStatus = $communityLicencesStatus;

        return $this;
    }

    /**
     * Get the community licences status
     *
     * @return int
     */
    public function getCommunityLicencesStatus()
    {
        return $this->communityLicencesStatus;
    }

    /**
     * Set the conditions undertakings status
     *
     * @param int $conditionsUndertakingsStatus
     * @return ApplicationCompletion
     */
    public function setConditionsUndertakingsStatus($conditionsUndertakingsStatus)
    {
        $this->conditionsUndertakingsStatus = $conditionsUndertakingsStatus;

        return $this;
    }

    /**
     * Get the conditions undertakings status
     *
     * @return int
     */
    public function getConditionsUndertakingsStatus()
    {
        return $this->conditionsUndertakingsStatus;
    }

    /**
     * Set the convictions penalties status
     *
     * @param int $convictionsPenaltiesStatus
     * @return ApplicationCompletion
     */
    public function setConvictionsPenaltiesStatus($convictionsPenaltiesStatus)
    {
        $this->convictionsPenaltiesStatus = $convictionsPenaltiesStatus;

        return $this;
    }

    /**
     * Get the convictions penalties status
     *
     * @return int
     */
    public function getConvictionsPenaltiesStatus()
    {
        return $this->convictionsPenaltiesStatus;
    }

    /**
     * Set the discs status
     *
     * @param int $discsStatus
     * @return ApplicationCompletion
     */
    public function setDiscsStatus($discsStatus)
    {
        $this->discsStatus = $discsStatus;

        return $this;
    }

    /**
     * Get the discs status
     *
     * @return int
     */
    public function getDiscsStatus()
    {
        return $this->discsStatus;
    }

    /**
     * Set the financial evidence status
     *
     * @param int $financialEvidenceStatus
     * @return ApplicationCompletion
     */
    public function setFinancialEvidenceStatus($financialEvidenceStatus)
    {
        $this->financialEvidenceStatus = $financialEvidenceStatus;

        return $this;
    }

    /**
     * Get the financial evidence status
     *
     * @return int
     */
    public function getFinancialEvidenceStatus()
    {
        return $this->financialEvidenceStatus;
    }

    /**
     * Set the financial history status
     *
     * @param int $financialHistoryStatus
     * @return ApplicationCompletion
     */
    public function setFinancialHistoryStatus($financialHistoryStatus)
    {
        $this->financialHistoryStatus = $financialHistoryStatus;

        return $this;
    }

    /**
     * Get the financial history status
     *
     * @return int
     */
    public function getFinancialHistoryStatus()
    {
        return $this->financialHistoryStatus;
    }

    /**
     * Set the last section
     *
     * @param string $lastSection
     * @return ApplicationCompletion
     */
    public function setLastSection($lastSection)
    {
        $this->lastSection = $lastSection;

        return $this;
    }

    /**
     * Get the last section
     *
     * @return string
     */
    public function getLastSection()
    {
        return $this->lastSection;
    }

    /**
     * Set the licence history status
     *
     * @param int $licenceHistoryStatus
     * @return ApplicationCompletion
     */
    public function setLicenceHistoryStatus($licenceHistoryStatus)
    {
        $this->licenceHistoryStatus = $licenceHistoryStatus;

        return $this;
    }

    /**
     * Get the licence history status
     *
     * @return int
     */
    public function getLicenceHistoryStatus()
    {
        return $this->licenceHistoryStatus;
    }

    /**
     * Set the operating centres status
     *
     * @param int $operatingCentresStatus
     * @return ApplicationCompletion
     */
    public function setOperatingCentresStatus($operatingCentresStatus)
    {
        $this->operatingCentresStatus = $operatingCentresStatus;

        return $this;
    }

    /**
     * Get the operating centres status
     *
     * @return int
     */
    public function getOperatingCentresStatus()
    {
        return $this->operatingCentresStatus;
    }

    /**
     * Set the people status
     *
     * @param int $peopleStatus
     * @return ApplicationCompletion
     */
    public function setPeopleStatus($peopleStatus)
    {
        $this->peopleStatus = $peopleStatus;

        return $this;
    }

    /**
     * Get the people status
     *
     * @return int
     */
    public function getPeopleStatus()
    {
        return $this->peopleStatus;
    }

    /**
     * Set the safety status
     *
     * @param int $safetyStatus
     * @return ApplicationCompletion
     */
    public function setSafetyStatus($safetyStatus)
    {
        $this->safetyStatus = $safetyStatus;

        return $this;
    }

    /**
     * Get the safety status
     *
     * @return int
     */
    public function getSafetyStatus()
    {
        return $this->safetyStatus;
    }

    /**
     * Set the taxi phv status
     *
     * @param int $taxiPhvStatus
     * @return ApplicationCompletion
     */
    public function setTaxiPhvStatus($taxiPhvStatus)
    {
        $this->taxiPhvStatus = $taxiPhvStatus;

        return $this;
    }

    /**
     * Get the taxi phv status
     *
     * @return int
     */
    public function getTaxiPhvStatus()
    {
        return $this->taxiPhvStatus;
    }

    /**
     * Set the transport managers status
     *
     * @param int $transportManagersStatus
     * @return ApplicationCompletion
     */
    public function setTransportManagersStatus($transportManagersStatus)
    {
        $this->transportManagersStatus = $transportManagersStatus;

        return $this;
    }

    /**
     * Get the transport managers status
     *
     * @return int
     */
    public function getTransportManagersStatus()
    {
        return $this->transportManagersStatus;
    }

    /**
     * Set the type of licence status
     *
     * @param int $typeOfLicenceStatus
     * @return ApplicationCompletion
     */
    public function setTypeOfLicenceStatus($typeOfLicenceStatus)
    {
        $this->typeOfLicenceStatus = $typeOfLicenceStatus;

        return $this;
    }

    /**
     * Get the type of licence status
     *
     * @return int
     */
    public function getTypeOfLicenceStatus()
    {
        return $this->typeOfLicenceStatus;
    }

    /**
     * Set the undertakings status
     *
     * @param int $undertakingsStatus
     * @return ApplicationCompletion
     */
    public function setUndertakingsStatus($undertakingsStatus)
    {
        $this->undertakingsStatus = $undertakingsStatus;

        return $this;
    }

    /**
     * Get the undertakings status
     *
     * @return int
     */
    public function getUndertakingsStatus()
    {
        return $this->undertakingsStatus;
    }

    /**
     * Set the vehicles declarations status
     *
     * @param int $vehiclesDeclarationsStatus
     * @return ApplicationCompletion
     */
    public function setVehiclesDeclarationsStatus($vehiclesDeclarationsStatus)
    {
        $this->vehiclesDeclarationsStatus = $vehiclesDeclarationsStatus;

        return $this;
    }

    /**
     * Get the vehicles declarations status
     *
     * @return int
     */
    public function getVehiclesDeclarationsStatus()
    {
        return $this->vehiclesDeclarationsStatus;
    }

    /**
     * Set the vehicles psv status
     *
     * @param int $vehiclesPsvStatus
     * @return ApplicationCompletion
     */
    public function setVehiclesPsvStatus($vehiclesPsvStatus)
    {
        $this->vehiclesPsvStatus = $vehiclesPsvStatus;

        return $this;
    }

    /**
     * Get the vehicles psv status
     *
     * @return int
     */
    public function getVehiclesPsvStatus()
    {
        return $this->vehiclesPsvStatus;
    }

    /**
     * Set the vehicles status
     *
     * @param int $vehiclesStatus
     * @return ApplicationCompletion
     */
    public function setVehiclesStatus($vehiclesStatus)
    {
        $this->vehiclesStatus = $vehiclesStatus;

        return $this;
    }

    /**
     * Get the vehicles status
     *
     * @return int
     */
    public function getVehiclesStatus()
    {
        return $this->vehiclesStatus;
    }
}
