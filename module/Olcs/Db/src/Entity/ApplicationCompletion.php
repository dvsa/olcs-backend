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
 *    }
 * )
 */
class ApplicationCompletion implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Identifier - Id
     *
     * @var \Olcs\Db\Entity\Application
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Olcs\Db\Entity\Application", fetch="LAZY")
     * @ORM\JoinColumn(name="id", referencedColumnName="id", nullable=false)
     */
    protected $id;

    /**
     * Section your business status
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_yb_status", nullable=true)
     */
    protected $sectionYourBusinessStatus;

    /**
     * Section your business business type status
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_yb_bt_status", nullable=true)
     */
    protected $sectionYourBusinessBusinessTypeStatus;

    /**
     * Section your business business details status
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_yb_bd_status", nullable=true)
     */
    protected $sectionYourBusinessBusinessDetailsStatus;

    /**
     * Section your business addresses status
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_yb_add_status", nullable=true)
     */
    protected $sectionYourBusinessAddressesStatus;

    /**
     * Section your business people status
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_yb_peo_status", nullable=true)
     */
    protected $sectionYourBusinessPeopleStatus;

    /**
     * Section type of licence status
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_tol_status", nullable=true)
     */
    protected $sectionTypeOfLicenceStatus;

    /**
     * Section type of licence operator location status
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_tol_ol_status", nullable=true)
     */
    protected $sectionTypeOfLicenceOperatorLocationStatus;

    /**
     * Section type of licence operator type status
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_tol_ot_status", nullable=true)
     */
    protected $sectionTypeOfLicenceOperatorTypeStatus;

    /**
     * Section type of licence licence type status
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_tol_lt_status", nullable=true)
     */
    protected $sectionTypeOfLicenceLicenceTypeStatus;

    /**
     * Section operating centres status
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_ocs_status", nullable=true)
     */
    protected $sectionOperatingCentresStatus;

    /**
     * Section operating centres authorisation status
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_ocs_auth_status", nullable=true)
     */
    protected $sectionOperatingCentresAuthorisationStatus;

    /**
     * Section operating centres financial evidence status
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_ocs_fe_status", nullable=true)
     */
    protected $sectionOperatingCentresFinancialEvidenceStatus;

    /**
     * Section transport managers status
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_tms_status", nullable=true)
     */
    protected $sectionTransportManagersStatus;

    /**
     * Section vehicle safety status
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_veh_status", nullable=true)
     */
    protected $sectionVehicleSafetyStatus;

    /**
     * Section vehicle safety vehicle status
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_veh_v_status", nullable=true)
     */
    protected $sectionVehicleSafetyVehicleStatus;

    /**
     * Section vehicle safety vehicle psv status
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_veh_vpsv_status", nullable=true)
     */
    protected $sectionVehicleSafetyVehiclePsvStatus;

    /**
     * Section vehicle safety safety status
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_veh_s_status", nullable=true)
     */
    protected $sectionVehicleSafetySafetyStatus;

    /**
     * Section previous history status
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_ph_status", nullable=true)
     */
    protected $sectionPreviousHistoryStatus;

    /**
     * Section previous history financial history status
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_ph_fh_status", nullable=true)
     */
    protected $sectionPreviousHistoryFinancialHistoryStatus;

    /**
     * Section previous history licence history status
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_ph_lh_status", nullable=true)
     */
    protected $sectionPreviousHistoryLicenceHistoryStatus;

    /**
     * Section previous history convictions penalties status
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_ph_cp_status", nullable=true)
     */
    protected $sectionPreviousHistoryConvictionsPenaltiesStatus;

    /**
     * Section review declarations status
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_rd_status", nullable=true)
     */
    protected $sectionReviewDeclarationsStatus;

    /**
     * Section payment submission status
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_pay_status", nullable=true)
     */
    protected $sectionPaymentSubmissionStatus;

    /**
     * Section payment submission payment status
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_pay_pay_status", nullable=true)
     */
    protected $sectionPaymentSubmissionPaymentStatus;

    /**
     * Section payment submission summary status
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_pay_summary_status", nullable=true)
     */
    protected $sectionPaymentSubmissionSummaryStatus;

    /**
     * Section taxi phv status
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_tp_status", nullable=true)
     */
    protected $sectionTaxiPhvStatus;

    /**
     * Section taxi phv licence status
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="section_tp_lic_status", nullable=true)
     */
    protected $sectionTaxiPhvLicenceStatus;

    /**
     * Last section
     *
     * @var string
     *
     * @ORM\Column(type="string", name="last_section", length=255, nullable=true)
     */
    protected $lastSection;


    /**
     * Set the id
     *
     * @param \Olcs\Db\Entity\Application $id
     * @return ApplicationCompletion
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the id
     *
     * @return \Olcs\Db\Entity\Application
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the section your business status
     *
     * @param boolean $sectionYourBusinessStatus
     * @return ApplicationCompletion
     */
    public function setSectionYourBusinessStatus($sectionYourBusinessStatus)
    {
        $this->sectionYourBusinessStatus = $sectionYourBusinessStatus;

        return $this;
    }

    /**
     * Get the section your business status
     *
     * @return boolean
     */
    public function getSectionYourBusinessStatus()
    {
        return $this->sectionYourBusinessStatus;
    }

    /**
     * Set the section your business business type status
     *
     * @param boolean $sectionYourBusinessBusinessTypeStatus
     * @return ApplicationCompletion
     */
    public function setSectionYourBusinessBusinessTypeStatus($sectionYourBusinessBusinessTypeStatus)
    {
        $this->sectionYourBusinessBusinessTypeStatus = $sectionYourBusinessBusinessTypeStatus;

        return $this;
    }

    /**
     * Get the section your business business type status
     *
     * @return boolean
     */
    public function getSectionYourBusinessBusinessTypeStatus()
    {
        return $this->sectionYourBusinessBusinessTypeStatus;
    }

    /**
     * Set the section your business business details status
     *
     * @param boolean $sectionYourBusinessBusinessDetailsStatus
     * @return ApplicationCompletion
     */
    public function setSectionYourBusinessBusinessDetailsStatus($sectionYourBusinessBusinessDetailsStatus)
    {
        $this->sectionYourBusinessBusinessDetailsStatus = $sectionYourBusinessBusinessDetailsStatus;

        return $this;
    }

    /**
     * Get the section your business business details status
     *
     * @return boolean
     */
    public function getSectionYourBusinessBusinessDetailsStatus()
    {
        return $this->sectionYourBusinessBusinessDetailsStatus;
    }

    /**
     * Set the section your business addresses status
     *
     * @param boolean $sectionYourBusinessAddressesStatus
     * @return ApplicationCompletion
     */
    public function setSectionYourBusinessAddressesStatus($sectionYourBusinessAddressesStatus)
    {
        $this->sectionYourBusinessAddressesStatus = $sectionYourBusinessAddressesStatus;

        return $this;
    }

    /**
     * Get the section your business addresses status
     *
     * @return boolean
     */
    public function getSectionYourBusinessAddressesStatus()
    {
        return $this->sectionYourBusinessAddressesStatus;
    }

    /**
     * Set the section your business people status
     *
     * @param boolean $sectionYourBusinessPeopleStatus
     * @return ApplicationCompletion
     */
    public function setSectionYourBusinessPeopleStatus($sectionYourBusinessPeopleStatus)
    {
        $this->sectionYourBusinessPeopleStatus = $sectionYourBusinessPeopleStatus;

        return $this;
    }

    /**
     * Get the section your business people status
     *
     * @return boolean
     */
    public function getSectionYourBusinessPeopleStatus()
    {
        return $this->sectionYourBusinessPeopleStatus;
    }

    /**
     * Set the section type of licence status
     *
     * @param boolean $sectionTypeOfLicenceStatus
     * @return ApplicationCompletion
     */
    public function setSectionTypeOfLicenceStatus($sectionTypeOfLicenceStatus)
    {
        $this->sectionTypeOfLicenceStatus = $sectionTypeOfLicenceStatus;

        return $this;
    }

    /**
     * Get the section type of licence status
     *
     * @return boolean
     */
    public function getSectionTypeOfLicenceStatus()
    {
        return $this->sectionTypeOfLicenceStatus;
    }

    /**
     * Set the section type of licence operator location status
     *
     * @param boolean $sectionTypeOfLicenceOperatorLocationStatus
     * @return ApplicationCompletion
     */
    public function setSectionTypeOfLicenceOperatorLocationStatus($sectionTypeOfLicenceOperatorLocationStatus)
    {
        $this->sectionTypeOfLicenceOperatorLocationStatus = $sectionTypeOfLicenceOperatorLocationStatus;

        return $this;
    }

    /**
     * Get the section type of licence operator location status
     *
     * @return boolean
     */
    public function getSectionTypeOfLicenceOperatorLocationStatus()
    {
        return $this->sectionTypeOfLicenceOperatorLocationStatus;
    }

    /**
     * Set the section type of licence operator type status
     *
     * @param boolean $sectionTypeOfLicenceOperatorTypeStatus
     * @return ApplicationCompletion
     */
    public function setSectionTypeOfLicenceOperatorTypeStatus($sectionTypeOfLicenceOperatorTypeStatus)
    {
        $this->sectionTypeOfLicenceOperatorTypeStatus = $sectionTypeOfLicenceOperatorTypeStatus;

        return $this;
    }

    /**
     * Get the section type of licence operator type status
     *
     * @return boolean
     */
    public function getSectionTypeOfLicenceOperatorTypeStatus()
    {
        return $this->sectionTypeOfLicenceOperatorTypeStatus;
    }

    /**
     * Set the section type of licence licence type status
     *
     * @param boolean $sectionTypeOfLicenceLicenceTypeStatus
     * @return ApplicationCompletion
     */
    public function setSectionTypeOfLicenceLicenceTypeStatus($sectionTypeOfLicenceLicenceTypeStatus)
    {
        $this->sectionTypeOfLicenceLicenceTypeStatus = $sectionTypeOfLicenceLicenceTypeStatus;

        return $this;
    }

    /**
     * Get the section type of licence licence type status
     *
     * @return boolean
     */
    public function getSectionTypeOfLicenceLicenceTypeStatus()
    {
        return $this->sectionTypeOfLicenceLicenceTypeStatus;
    }

    /**
     * Set the section operating centres status
     *
     * @param boolean $sectionOperatingCentresStatus
     * @return ApplicationCompletion
     */
    public function setSectionOperatingCentresStatus($sectionOperatingCentresStatus)
    {
        $this->sectionOperatingCentresStatus = $sectionOperatingCentresStatus;

        return $this;
    }

    /**
     * Get the section operating centres status
     *
     * @return boolean
     */
    public function getSectionOperatingCentresStatus()
    {
        return $this->sectionOperatingCentresStatus;
    }

    /**
     * Set the section operating centres authorisation status
     *
     * @param boolean $sectionOperatingCentresAuthorisationStatus
     * @return ApplicationCompletion
     */
    public function setSectionOperatingCentresAuthorisationStatus($sectionOperatingCentresAuthorisationStatus)
    {
        $this->sectionOperatingCentresAuthorisationStatus = $sectionOperatingCentresAuthorisationStatus;

        return $this;
    }

    /**
     * Get the section operating centres authorisation status
     *
     * @return boolean
     */
    public function getSectionOperatingCentresAuthorisationStatus()
    {
        return $this->sectionOperatingCentresAuthorisationStatus;
    }

    /**
     * Set the section operating centres financial evidence status
     *
     * @param boolean $sectionOperatingCentresFinancialEvidenceStatus
     * @return ApplicationCompletion
     */
    public function setSectionOperatingCentresFinancialEvidenceStatus($sectionOperatingCentresFinancialEvidenceStatus)
    {
        $this->sectionOperatingCentresFinancialEvidenceStatus = $sectionOperatingCentresFinancialEvidenceStatus;

        return $this;
    }

    /**
     * Get the section operating centres financial evidence status
     *
     * @return boolean
     */
    public function getSectionOperatingCentresFinancialEvidenceStatus()
    {
        return $this->sectionOperatingCentresFinancialEvidenceStatus;
    }

    /**
     * Set the section transport managers status
     *
     * @param boolean $sectionTransportManagersStatus
     * @return ApplicationCompletion
     */
    public function setSectionTransportManagersStatus($sectionTransportManagersStatus)
    {
        $this->sectionTransportManagersStatus = $sectionTransportManagersStatus;

        return $this;
    }

    /**
     * Get the section transport managers status
     *
     * @return boolean
     */
    public function getSectionTransportManagersStatus()
    {
        return $this->sectionTransportManagersStatus;
    }

    /**
     * Set the section vehicle safety status
     *
     * @param boolean $sectionVehicleSafetyStatus
     * @return ApplicationCompletion
     */
    public function setSectionVehicleSafetyStatus($sectionVehicleSafetyStatus)
    {
        $this->sectionVehicleSafetyStatus = $sectionVehicleSafetyStatus;

        return $this;
    }

    /**
     * Get the section vehicle safety status
     *
     * @return boolean
     */
    public function getSectionVehicleSafetyStatus()
    {
        return $this->sectionVehicleSafetyStatus;
    }

    /**
     * Set the section vehicle safety vehicle status
     *
     * @param boolean $sectionVehicleSafetyVehicleStatus
     * @return ApplicationCompletion
     */
    public function setSectionVehicleSafetyVehicleStatus($sectionVehicleSafetyVehicleStatus)
    {
        $this->sectionVehicleSafetyVehicleStatus = $sectionVehicleSafetyVehicleStatus;

        return $this;
    }

    /**
     * Get the section vehicle safety vehicle status
     *
     * @return boolean
     */
    public function getSectionVehicleSafetyVehicleStatus()
    {
        return $this->sectionVehicleSafetyVehicleStatus;
    }

    /**
     * Set the section vehicle safety vehicle psv status
     *
     * @param boolean $sectionVehicleSafetyVehiclePsvStatus
     * @return ApplicationCompletion
     */
    public function setSectionVehicleSafetyVehiclePsvStatus($sectionVehicleSafetyVehiclePsvStatus)
    {
        $this->sectionVehicleSafetyVehiclePsvStatus = $sectionVehicleSafetyVehiclePsvStatus;

        return $this;
    }

    /**
     * Get the section vehicle safety vehicle psv status
     *
     * @return boolean
     */
    public function getSectionVehicleSafetyVehiclePsvStatus()
    {
        return $this->sectionVehicleSafetyVehiclePsvStatus;
    }

    /**
     * Set the section vehicle safety safety status
     *
     * @param boolean $sectionVehicleSafetySafetyStatus
     * @return ApplicationCompletion
     */
    public function setSectionVehicleSafetySafetyStatus($sectionVehicleSafetySafetyStatus)
    {
        $this->sectionVehicleSafetySafetyStatus = $sectionVehicleSafetySafetyStatus;

        return $this;
    }

    /**
     * Get the section vehicle safety safety status
     *
     * @return boolean
     */
    public function getSectionVehicleSafetySafetyStatus()
    {
        return $this->sectionVehicleSafetySafetyStatus;
    }

    /**
     * Set the section previous history status
     *
     * @param boolean $sectionPreviousHistoryStatus
     * @return ApplicationCompletion
     */
    public function setSectionPreviousHistoryStatus($sectionPreviousHistoryStatus)
    {
        $this->sectionPreviousHistoryStatus = $sectionPreviousHistoryStatus;

        return $this;
    }

    /**
     * Get the section previous history status
     *
     * @return boolean
     */
    public function getSectionPreviousHistoryStatus()
    {
        return $this->sectionPreviousHistoryStatus;
    }

    /**
     * Set the section previous history financial history status
     *
     * @param boolean $sectionPreviousHistoryFinancialHistoryStatus
     * @return ApplicationCompletion
     */
    public function setSectionPreviousHistoryFinancialHistoryStatus($sectionPreviousHistoryFinancialHistoryStatus)
    {
        $this->sectionPreviousHistoryFinancialHistoryStatus = $sectionPreviousHistoryFinancialHistoryStatus;

        return $this;
    }

    /**
     * Get the section previous history financial history status
     *
     * @return boolean
     */
    public function getSectionPreviousHistoryFinancialHistoryStatus()
    {
        return $this->sectionPreviousHistoryFinancialHistoryStatus;
    }

    /**
     * Set the section previous history licence history status
     *
     * @param boolean $sectionPreviousHistoryLicenceHistoryStatus
     * @return ApplicationCompletion
     */
    public function setSectionPreviousHistoryLicenceHistoryStatus($sectionPreviousHistoryLicenceHistoryStatus)
    {
        $this->sectionPreviousHistoryLicenceHistoryStatus = $sectionPreviousHistoryLicenceHistoryStatus;

        return $this;
    }

    /**
     * Get the section previous history licence history status
     *
     * @return boolean
     */
    public function getSectionPreviousHistoryLicenceHistoryStatus()
    {
        return $this->sectionPreviousHistoryLicenceHistoryStatus;
    }

    /**
     * Set the section previous history convictions penalties status
     *
     * @param boolean $sectionPreviousHistoryConvictionsPenaltiesStatus
     * @return ApplicationCompletion
     */
    public function setSectionPreviousHistoryConvictionsPenaltiesStatus($sectionPreviousHistoryConvictionsPenaltiesStatus)
    {
        $this->sectionPreviousHistoryConvictionsPenaltiesStatus = $sectionPreviousHistoryConvictionsPenaltiesStatus;

        return $this;
    }

    /**
     * Get the section previous history convictions penalties status
     *
     * @return boolean
     */
    public function getSectionPreviousHistoryConvictionsPenaltiesStatus()
    {
        return $this->sectionPreviousHistoryConvictionsPenaltiesStatus;
    }

    /**
     * Set the section review declarations status
     *
     * @param boolean $sectionReviewDeclarationsStatus
     * @return ApplicationCompletion
     */
    public function setSectionReviewDeclarationsStatus($sectionReviewDeclarationsStatus)
    {
        $this->sectionReviewDeclarationsStatus = $sectionReviewDeclarationsStatus;

        return $this;
    }

    /**
     * Get the section review declarations status
     *
     * @return boolean
     */
    public function getSectionReviewDeclarationsStatus()
    {
        return $this->sectionReviewDeclarationsStatus;
    }

    /**
     * Set the section payment submission status
     *
     * @param boolean $sectionPaymentSubmissionStatus
     * @return ApplicationCompletion
     */
    public function setSectionPaymentSubmissionStatus($sectionPaymentSubmissionStatus)
    {
        $this->sectionPaymentSubmissionStatus = $sectionPaymentSubmissionStatus;

        return $this;
    }

    /**
     * Get the section payment submission status
     *
     * @return boolean
     */
    public function getSectionPaymentSubmissionStatus()
    {
        return $this->sectionPaymentSubmissionStatus;
    }

    /**
     * Set the section payment submission payment status
     *
     * @param boolean $sectionPaymentSubmissionPaymentStatus
     * @return ApplicationCompletion
     */
    public function setSectionPaymentSubmissionPaymentStatus($sectionPaymentSubmissionPaymentStatus)
    {
        $this->sectionPaymentSubmissionPaymentStatus = $sectionPaymentSubmissionPaymentStatus;

        return $this;
    }

    /**
     * Get the section payment submission payment status
     *
     * @return boolean
     */
    public function getSectionPaymentSubmissionPaymentStatus()
    {
        return $this->sectionPaymentSubmissionPaymentStatus;
    }

    /**
     * Set the section payment submission summary status
     *
     * @param boolean $sectionPaymentSubmissionSummaryStatus
     * @return ApplicationCompletion
     */
    public function setSectionPaymentSubmissionSummaryStatus($sectionPaymentSubmissionSummaryStatus)
    {
        $this->sectionPaymentSubmissionSummaryStatus = $sectionPaymentSubmissionSummaryStatus;

        return $this;
    }

    /**
     * Get the section payment submission summary status
     *
     * @return boolean
     */
    public function getSectionPaymentSubmissionSummaryStatus()
    {
        return $this->sectionPaymentSubmissionSummaryStatus;
    }

    /**
     * Set the section taxi phv status
     *
     * @param boolean $sectionTaxiPhvStatus
     * @return ApplicationCompletion
     */
    public function setSectionTaxiPhvStatus($sectionTaxiPhvStatus)
    {
        $this->sectionTaxiPhvStatus = $sectionTaxiPhvStatus;

        return $this;
    }

    /**
     * Get the section taxi phv status
     *
     * @return boolean
     */
    public function getSectionTaxiPhvStatus()
    {
        return $this->sectionTaxiPhvStatus;
    }

    /**
     * Set the section taxi phv licence status
     *
     * @param boolean $sectionTaxiPhvLicenceStatus
     * @return ApplicationCompletion
     */
    public function setSectionTaxiPhvLicenceStatus($sectionTaxiPhvLicenceStatus)
    {
        $this->sectionTaxiPhvLicenceStatus = $sectionTaxiPhvLicenceStatus;

        return $this;
    }

    /**
     * Get the section taxi phv licence status
     *
     * @return boolean
     */
    public function getSectionTaxiPhvLicenceStatus()
    {
        return $this->sectionTaxiPhvLicenceStatus;
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
}
