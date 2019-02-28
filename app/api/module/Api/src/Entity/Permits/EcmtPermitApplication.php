<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use DateTime;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Entity\CancelableInterface;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\OrganisationProviderInterface;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\Traits\TieredProductReference;

/**
 * EcmtPermitApplication Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="ecmt_permit_application",
 *    indexes={
 *        @ORM\Index(name="ix_ecmt_permit_application_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_ecmt_permit_application_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_ecmt_permit_application_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_ecmt_permit_application_permit_type", columns={"permit_type"}),
 *        @ORM\Index(name="ix_ecmt_permit_application_status", columns={"status"}),
 *        @ORM\Index(name="ix_ecmt_permit_application_sectors_id", columns={"sectors_id"})
 *    }
 * )
 */
class EcmtPermitApplication extends AbstractEcmtPermitApplication implements OrganisationProviderInterface, CancelableInterface
{
    use TieredProductReference;

    const STATUS_CANCELLED = 'permit_app_cancelled';
    const STATUS_NOT_YET_SUBMITTED = 'permit_app_nys';
    const STATUS_UNDER_CONSIDERATION = 'permit_app_uc';
    const STATUS_WITHDRAWN = 'permit_app_withdrawn';
    const STATUS_AWAITING_FEE = 'permit_app_awaiting';
    const STATUS_FEE_PAID = 'permit_app_fee_paid';
    const STATUS_UNSUCCESSFUL = 'permit_app_unsuccessful';
    const STATUS_ISSUED = 'permit_app_issued';
    const STATUS_ISSUING = 'permit_app_issuing';
    const STATUS_VALID = 'permit_app_valid';
    const STATUS_DECLINED = 'permit_app_declined';

    const SOURCE_INTERNAL = 'app_source_internal';
    const SOURCE_SELFSERVE = 'app_source_selfserve';

    const NOTIFICATION_TYPE_EMAIL = 'notification_type_email';
    const NOTIFICATION_TYPE_MANUAL = 'notification_type_manual';

    const PERMIT_TYPE = 'permit_ecmt';
    const PERMIT_TEMPLATE_NAME = 'IRHP_PERMIT_ECMT';
    const PERMIT_COVERING_LETTER_TEMPLATE_NAME = 'IRHP_PERMIT_ECMT_COVERING_LETTER';

    const SECTION_COMPLETION_CANNOT_START = 'ecmt_section_sts_csy';
    const SECTION_COMPLETION_NOT_STARTED = 'ecmt_section_sts_nys';
    const SECTION_COMPLETION_COMPLETED = 'ecmt_section_sts_com';

    const INTER_JOURNEY_LESS_60 = 'inter_journey_less_60';
    const INTER_JOURNEY_60_90 = 'inter_journey_60_90';
    const INTER_JOURNEY_MORE_90 = 'inter_journey_more_90';

    const WITHDRAWN_REASON_UNPAID = 'permits_app_withdraw_not_paid';
    const WITHDRAWN_REASON_BY_USER = 'permits_app_withdraw_by_user';
    const WITHDRAWN_REASON_DECLINED = 'permits_app_withdraw_declined';

    const SUCCESS_LEVEL_FULL = 'success_level_full';
    const SUCCESS_LEVEL_PARTIAL = 'success_level_partial';
    const SUCCESS_LEVEL_NONE = 'success_level_none';

    /**
     * @todo this needs to be much more robust, not least because how we store certain data is going to change
     */
    const SECTIONS = [
        'licence' => 'fieldIsNotNull',
        'emissions' => 'fieldIsAgreed',
        'cabotage' => 'fieldIsAgreed',
        'internationalJourneys' => 'fieldIsNotNull',
        'trips' => 'fieldIsInt',
        'permitsRequired' => 'fieldIsInt',
        'sectors' => 'fieldIsNotNull',
        'countrys' => 'countrysPopulated',
    ];

    /**
     * @todo this needs to be much more robust, not least because how we store certain data is going to change
     */
    const CONFIRMATION_SECTIONS = [
        'checkedAnswers' => 'fieldIsAgreed',
        'declaration' => 'fieldIsAgreed',
    ];

    const INTERNATIONAL_JOURNEYS_DECIMAL_MAP = [
        self::INTER_JOURNEY_LESS_60 => 0.3,
        self::INTER_JOURNEY_60_90 => 0.75,
        self::INTER_JOURNEY_MORE_90 => 1
    ];

    const ISSUE_FEE_PRODUCT_REFERENCE_MONTH_ARRAY = [
        'Jan' => FeeType::FEE_TYPE_ECMT_ISSUE_100_PRODUCT_REF,
        'Feb' => FeeType::FEE_TYPE_ECMT_ISSUE_100_PRODUCT_REF,
        'Mar' => FeeType::FEE_TYPE_ECMT_ISSUE_100_PRODUCT_REF,
        'Apr' => FeeType::FEE_TYPE_ECMT_ISSUE_75_PRODUCT_REF,
        'May' => FeeType::FEE_TYPE_ECMT_ISSUE_75_PRODUCT_REF,
        'Jun' => FeeType::FEE_TYPE_ECMT_ISSUE_75_PRODUCT_REF,
        'Jul' => FeeType::FEE_TYPE_ECMT_ISSUE_50_PRODUCT_REF,
        'Aug' => FeeType::FEE_TYPE_ECMT_ISSUE_50_PRODUCT_REF,
        'Sep' => FeeType::FEE_TYPE_ECMT_ISSUE_50_PRODUCT_REF,
        'Oct' => FeeType::FEE_TYPE_ECMT_ISSUE_25_PRODUCT_REF,
        'Nov' => FeeType::FEE_TYPE_ECMT_ISSUE_25_PRODUCT_REF,
        'Dec' => FeeType::FEE_TYPE_ECMT_ISSUE_25_PRODUCT_REF,
    ];

    /**
     * Prepares data and calls TieredProductReference Trait method genericGetProdRefForTier
     *
     * @param DateTime $now
     * @return string
     */
    public function getProductReferenceForTier(DateTime $now = null)
    {
        $now = is_null($now) ? new DateTime() : $now;
        $irhpPermitApplication = $this->getFirstIrhpPermitApplication();
        $irhpPermitStock = $irhpPermitApplication->getIrhpPermitWindow()->getIrhpPermitStock();
        return $this->genericGetProdRefForTier(
            $irhpPermitStock->getValidFrom(true),
            $irhpPermitStock->getValidTo(true),
            $now,
            self::ISSUE_FEE_PRODUCT_REFERENCE_MONTH_ARRAY
        );
    }

    /**
     * Create new EcmtPermitApplication
     *
     * @param RefData $source Source
     * @param RefData $status Status
     * @param RefData $permitType Permit type
     * @param Licence $licence Licence
     * @param string|null $dateReceived
     * @param Sectors|null $sectors
     * @param ArrayCollection $countrys
     * @param int|null $cabotage
     * @param int|null $declaration
     * @param int|null $emissions
     * @param int|null $permitsRequired
     * @param int|null $trips
     * @param RefData $internationalJourneys
     *
     * @return EcmtPermitApplication
     */
    public static function createNewInternal(
        RefData $source,
        RefData $status,
        RefData $permitType,
        Licence $licence,
        string $dateReceived = null,
        Sectors $sectors = null,
        ArrayCollection $countrys,
        int $cabotage = null,
        int $declaration = null,
        int $emissions = null,
        int $permitsRequired = null,
        int $trips = null,
        RefData $internationalJourneys = null
    ) {
        $ecmtPermitApplication = new self();
        $ecmtPermitApplication->source = $source;
        $ecmtPermitApplication->status = $status;
        $ecmtPermitApplication->permitType = $permitType;
        $ecmtPermitApplication->licence = $licence;
        $ecmtPermitApplication->sectors = $sectors;
        $ecmtPermitApplication->updateCountrys($countrys);
        $ecmtPermitApplication->cabotage = $cabotage;
        $ecmtPermitApplication->declaration = $declaration;
        $ecmtPermitApplication->emissions = $emissions;
        $ecmtPermitApplication->permitsRequired = $permitsRequired;
        $ecmtPermitApplication->trips = $trips;
        $ecmtPermitApplication->internationalJourneys = $internationalJourneys;
        $ecmtPermitApplication->dateReceived = static::processDate($dateReceived);

        // If Internal user has completed all fields and declaration question in one step set checked answers to 1 to
        // allow submission without a second save step required as per John S request
        $sections = $ecmtPermitApplication->getSectionCompletion(self::SECTIONS);
        if ($sections['allCompleted'] && $declaration == 1) {
            $ecmtPermitApplication->checkedAnswers = 1;
        }

        return $ecmtPermitApplication;
    }


    /**
     * Create new EcmtPermitApplication
     *
     * @param RefData $source Source
     * @param RefData $status Status
     * @param RefData $permitType Permit type
     * @param Licence $licence Licence
     * @param string|null $dateReceived
     *
     * @return EcmtPermitApplication
     */
    public static function createNew(
        RefData $source,
        RefData $status,
        RefData $permitType,
        Licence $licence,
        string $dateReceived = null
    ) {
        $ecmtPermitApplication = new self();
        $ecmtPermitApplication->source = $source;
        $ecmtPermitApplication->status = $status;
        $ecmtPermitApplication->permitType = $permitType;
        $ecmtPermitApplication->licence = $licence;
        $ecmtPermitApplication->dateReceived = static::processDate($dateReceived);
        return $ecmtPermitApplication;
    }

    /**
     * Update EcmtPermitApplication
     *
     * @param RefData $permitType Permit type
     * @param Licence $licence Licence
     * @param Sectors|null $sectors
     * @param $countrys
     * @param int|null $cabotage
     * @param int|null $declaration
     * @param int|null $emissions
     * @param int|null $permitsRequired
     * @param int|null $trips
     * @param RefData $internationalJourneys
     * @param string|null $dateReceived
     *
     * @return EcmtPermitApplication
     */
    public function update(
        ?RefData $permitType,
        Licence $licence,
        ?Sectors $sectors = null,
        $countrys = null,
        int $cabotage = null,
        int $declaration = null,
        int $emissions = null,
        int $permitsRequired = null,
        int $trips = null,
        RefData $internationalJourneys = null,
        string $dateReceived = null
    ) {
        $this->permitType = $permitType ?? $this->permitType;
        $this->licence = $licence;
        $this->sectors = $sectors;
        $this->updateCountrys($countrys);
        $this->cabotage = $cabotage;
        $this->checkedAnswers = $declaration; //auto updated alongside declaration for internal apps
        $this->declaration = $declaration;
        $this->emissions = $emissions;
        $this->permitsRequired = $permitsRequired;
        $this->trips = $trips;
        $this->internationalJourneys = $internationalJourneys;
        $this->dateReceived = $this->processDate($dateReceived);

        return $this;
    }

    /**
     * Submit the app
     *
     * @param RefData $submitStatus
     *
     * @return void
     * @throws ForbiddenException
     */
    public function submit(RefData $submitStatus)
    {
        if (!$this->canBeSubmitted()) {
            throw new ForbiddenException('This application is not allowed to be submitted');
        }

        $this->status = $submitStatus;
    }

    public function withdraw(RefData $withdrawStatus, RefData $withdrawReason)
    {
        if (!$this->canBeWithdrawn()) {
            throw new ForbiddenException('This application is not allowed to be withdrawn');
        }

        $this->status = $withdrawStatus;
        $this->withdrawReason = $withdrawReason;
    }

    public function decline(RefData $declineStatus, RefData $withdrawReason)
    {
        if (!$this->canBeDeclined()) {
            throw new ForbiddenException('This application is not allowed to be declined');
        }

        $this->status = $declineStatus;
        $this->withdrawReason = $withdrawReason;
    }

    public function accept(RefData $acceptStatus)
    {
        if (!$this->canBeAccepted()) {
            throw new ForbiddenException('This application is not allowed to be accepted');
        }

        $this->status = $acceptStatus;
    }

    public function cancel(RefData $cancelStatus)
    {
        if (!$this->canBeCancelled()) {
            throw new ForbiddenException('This application is not allowed to be cancelled');
        }

        $this->status = $cancelStatus;
    }

    public function proceedToIssuing(RefData $issuingStatus)
    {
        if (!$this->isReadyForIssuing()) {
            throw new ForbiddenException('This application is not in the correct state to proceed to issuing');
        }

        $this->status = $issuingStatus;
    }

    public function proceedToValid(RefData $issuedStatus)
    {
        if (!$this->isIssueInProgress()) {
            throw new ForbiddenException('This application is not in the correct state to proceed to valid (' . $this->status->getId() . ')');
        }

        $this->status = $issuedStatus;
    }

    /**
     * Returns the emissions category defined against the IrhpPermitWindow
     *
     * @return string
     */
    public function getWindowEmissionsCategory()
    {
        $irhpPermitApplication = $this->getFirstIrhpPermitApplication();
        return $irhpPermitApplication->getIrhpPermitWindow()->getEmissionsCategory()->getId();
    }

    /**
     * Proceeds the application from under consideration to awaiting fee during the accept scoring process
     *
     * @param RefData $awaitingFeeStatus
     *
     * @throws ForbiddenException
     */
    public function proceedToAwaitingFee(RefData $awaitingFeeStatus)
    {
        if (!$this->isUnderConsideration()) {
            throw new ForbiddenException('This application is not in the correct state to proceed to awaiting fee ('.$this->status->getId().')');
        }

        $this->status = $awaitingFeeStatus;
    }

    /**
     * Proceeds the application from under consideration to unsuccessful during the accept scoring process
     *
     * @param RefData $unsuccessfulStatus
     *
     * @throws ForbiddenException
     */
    public function proceedToUnsuccessful(RefData $unsuccessfulStatus)
    {
        if (!$this->isUnderConsideration()) {
            throw new ForbiddenException('This application is not in the correct state to proceed to unsuccessful ('.$this->status->getId().')');
        }

        $this->status = $unsuccessfulStatus;
    }

    /**
     * Gets calculated values
     *
     * @return array
     */
    public function getCalculatedBundleValues()
    {
        $sectionCompletion = $this->getSectionCompletion(self::SECTIONS);

        return [
            'applicationRef' => $this->getApplicationRef(),
            'canBeCancelled' => $this->canBeCancelled(),
            'canBeSubmitted' => $this->canBeSubmitted(),
            'canBeWithdrawn' => $this->canBeWithdrawn(),
            'canBeUpdated' => $this->canBeUpdated(),
            'canBeAccepted' => $this->canBeAccepted(),
            'canBeDeclined' => $this->canBeDeclined(),
            'canCheckAnswers' => $this->canCheckAnswers(),
            'hasCheckedAnswers' => $this->hasCheckedAnswers(),
            'canMakeDeclaration' => $this->canMakeDeclaration(),
            'hasMadeDeclaration' => $this->hasMadeDeclaration(),
            'isNotYetSubmitted' => $this->isNotYetSubmitted(),
            'isUnderConsideration' => $this->isUnderConsideration(),
            'permitIntensityOfUse' => $this->getPermitIntensityOfUse(),
            'isCancelled' => $this->isCancelled(),
            'isWithdrawn' => $this->isWithdrawn(),
            'isAwaitingFee' => $this->isAwaitingFee(),
            'isFeePaid' => $this->isFeePaid(),
            'isIssueInProgress' => $this->isIssueInProgress(),
            'isValid' => $this->isValid(),
            'isActive' => $this->isActive(),
            'confirmationSectionCompletion' => $this->getSectionCompletion(self::CONFIRMATION_SECTIONS),
            'sectionCompletion' => $sectionCompletion,
            'hasOutstandingFees' => $this->hasOutstandingFees(),
            'windowEmissionsCategory' => $this->getWindowEmissionsCategory()
        ];
    }

    /**
     * Reset application answers - sets properties to null, or calls individual update methods in more important cases
     *
     * @return void
     */
    public function clearAnswers()
    {
        $this->emissions = null;
        $this->cabotage = null;
        $this->trips = null;
        $this->internationalJourneys = null;
        $this->sectors = null;
        $this->updatePermitsRequired(null);
        $this->resetCountrys();
        $this->resetCheckAnswersAndDeclaration();
    }

    /**
     * @param Licence $licence
     *
     * @return void
     */
    public function updateLicence(Licence $licence)
    {
        $this->licence = $licence;
        $this->clearAnswers();
    }

    /**
     * Updates the application to reflect whether or not cabotage will be carried out. A value of true indicates that
     * cabotage will NOT be carried out on the permit
     *
     * @param bool $cabotage
     */
    public function updateCabotage($cabotage)
    {
        $this->cabotage = $cabotage;
        $this->resetCheckAnswersAndDeclaration();
    }

    /**
     * Updates the application to reflect whether or not the permit will be used only by vehicles compliant with
     * Euro 6 standards. A value of true indicates that the permit will only be used by compliant vehicles
     *
     * @param bool $emissions
     */
    public function updateEmissions($emissions)
    {
        $this->emissions = $emissions;
        $this->resetCheckAnswersAndDeclaration();
    }

    /**
     * Updates the application to indicate the intended use of the permit in any countries that have imposed limits
     * on the issue of permits for UK hauliers. The $countrys parameter should be an array of Country objects.
     *
     * @param ArrayCollection $countrys
     */
    public function updateCountrys(ArrayCollection $countrys)
    {
        $this->countrys = $countrys;
        $this->hasRestrictedCountries = $countrys->count() > 0;

        $this->resetCheckAnswersAndDeclaration();
    }

    /**
     * Updates the application to indicate the number of required permits
     *
     * @param mixed $permitsRequired
     */
    public function updatePermitsRequired($permitsRequired)
    {
        $this->permitsRequired = $permitsRequired;
        $this->resetCheckAnswersAndDeclaration();
    }

    /**
     * Updates the application to indicate the number of trips made in the last 12 months using this licence
     *
     * @param int $trips
     */
    public function updateTrips($trips)
    {
        $this->trips = $trips;
        $this->resetCheckAnswersAndDeclaration();
    }

    /**
     * Updates the application to indicate a sector in which the haulier specialises
     *
     * @param RefData $internationalJourneys
     */
    public function updateInternationalJourneys(RefData $internationalJourneys)
    {
        $this->internationalJourneys = $internationalJourneys;
        $this->resetCheckAnswersAndDeclaration();
    }

    /**
     * Updates the application to indicate a sector in which the haulier specialises
     *
     * @param mixed $sectors
     */
    public function updateSectors($sectors)
    {
        $this->sectors = $sectors;
        $this->resetCheckAnswersAndDeclaration();
    }

    /**
     * Updates the application to indicate a sector in which the haulier specialises
     *
     * @param mixed $sectors
     */
    public function completeIssueFee($status)
    {
        $this->status = $status;
    }

    /**
     * Reset the checked answers and declaration sections to a value representing 'not completed'
     */
    private function resetCheckAnswersAndDeclaration()
    {
        $this->declaration = false;
        $this->checkedAnswers = false;
    }

    /**
     * Resets the countries that are associated with the ECMT Permit Application.
     * Also ensures that the hasRestrictedCountries variable is set to null to reset the 'completed' status.
     *
     * @return void
     */
    public function resetCountrys()
    {
        $this->countrys = new ArrayCollection();
        $this->hasRestrictedCountries = null;
    }

    /**
     * @todo this needs to be much more robust, not least because how we store certain data is going to change
     */
    protected function getSectionCompletion($sections)
    {
        $sectionCompletion = [];
        $totalCompleted = 0;
        $totalSections = count($sections);

        foreach ($sections as $field => $validator) {
            //default field to not started
            $status = self::SECTION_COMPLETION_NOT_STARTED;
            $fieldCompleted = $this->$validator($field);

            //if field completed, increment the completed number, and set the status
            if ($fieldCompleted) {
                $totalCompleted++;
                $status = self::SECTION_COMPLETION_COMPLETED;
            }

            $sectionCompletion[$field] = $status;
        }

        $sectionCompletion['totalSections'] = $totalSections;
        $sectionCompletion['totalCompleted'] = $totalCompleted;
        $sectionCompletion['allCompleted'] = ($totalSections === $totalCompleted);

        return $sectionCompletion;
    }

    /**
     * Checks an array collection has records
     *
     * @param string $field field being checked
     *
     * @return bool
     */
    private function collectionHasRecord($field)
    {
        return (bool)$this->$field->count();
    }

    /**
     * @param string $field field being checked
     *
     * @return bool
     */
    private function fieldIsAgreed($field)
    {
        return $this->$field == true;
    }

    /**
     * @param string $field field being checked
     *
     * @return bool
     */
    private function fieldIsNotNull($field)
    {
        return $this->$field !== null;
    }

    /**
     * @param string $field field being checked
     *
     * @return bool
     */
    private function fieldIsInt($field)
    {
        return is_int($this->$field);
    }

    /**
     * This is a custom validator for the countrys field
     * It isn't completely dynamic because it's assumed that
     * this won't be needed in the futuree
     *
     * @param string $field field being checked
     *
     * @return bool
     */
    private function countrysPopulated($field)
    {
        if ($this->hasRestrictedCountries === false) {
            return true;
        }

        return $this->collectionHasRecord($field);
    }

    /**
     * Get the application reference
     *
     * @return string
     */
    public function getApplicationRef()
    {
        return $this->licence->getLicNo() . ' / ' . $this->id;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->isNotYetSubmitted() || $this->isUnderConsideration() || $this->isAwaitingFee() || $this->isFeePaid();
    }

    /**
     * @return bool
     */
    public function isNotYetSubmitted()
    {
        return $this->status->getId() === self::STATUS_NOT_YET_SUBMITTED;
    }

    /**
     * @return bool
     */
    public function isUnderConsideration()
    {
        return $this->status->getId() === self::STATUS_UNDER_CONSIDERATION;
    }

    /**
     * @return bool
     */
    public function isCancelled()
    {
        return $this->status->getId() === self::STATUS_CANCELLED;
    }

    /**
     * @return bool
     */
    public function isWithdrawn()
    {
        return $this->status->getId() === self::STATUS_WITHDRAWN;
    }

    /**
     * @return bool
     */
    public function isAwaitingFee()
    {
        return $this->status->getId() === self::STATUS_AWAITING_FEE;
    }

    /**
     * @return bool
     */
    public function isFeePaid()
    {
        return $this->status->getId() === self::STATUS_FEE_PAID;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->status->getId() === self::STATUS_VALID;
    }

    /**
     * Whether the permit application can be submitted
     * @todo this currently reruns the section completion checks, should store the value instead for speed
     *
     * @return bool
     */
    public function canBeSubmitted()
    {
        if (!$this->isNotYetSubmitted()) {
            return false;
        }

        $confirmationSections = $this->getSectionCompletion(self::CONFIRMATION_SECTIONS);

        if (!$confirmationSections['allCompleted']) {
            return false;
        }

        $applicationSections = $this->getSectionCompletion(self::SECTIONS);

        if (!$applicationSections['allCompleted']) {
            return false;
        }

        return $this->licence->canMakeEcmtApplication($this);
    }

    /**
     * Whether the permit application can be updated
     *
     * @return bool
     */
    public function canBeUpdated()
    {
        return $this->isNotYetSubmitted();
    }

    /**
     * Whether a declaration can be made
     * @todo currently reruns section checks, these should be stored for speed reasons
     *
     * @return bool
     */
    public function canCheckAnswers()
    {
        $sections = $this->getSectionCompletion(self::SECTIONS);

        return $sections['allCompleted'] && $this->canBeUpdated();
    }

    /**
     * Whether a declaration can be made
     * @todo currently reruns section checks through canCheckAnswers(), these should be stored for speed reasons
     *
     * @return bool
     */
    public function canMakeDeclaration()
    {
        return $this->hasCheckedAnswers() && $this->canCheckAnswers();
    }

    /**
     * have the answers been checked
     *
     * @return bool
     */
    public function hasCheckedAnswers()
    {
        return $this->fieldIsAgreed('checkedAnswers');
    }

    /**
     * have the answers been checked
     *
     * @return bool
     */
    public function hasMadeDeclaration()
    {
        return $this->fieldIsAgreed('declaration');
    }

    /**
     * Whether the permit application can be withdrawn
     *
     * @return bool
     */
    public function canBeWithdrawn()
    {
        return $this->isUnderConsideration() || ($this->isAwaitingFee() && $this->issueFeeOverdue());
    }

    /**
     * Whether the permit application can be declined
     *
     * @return bool
     */
    public function canBeDeclined()
    {
        return $this->isAwaitingFee();
    }

    /**
     * Whether the permit application can be accepted
     *
     * @return bool
     */
    public function canBeAccepted()
    {
        return $this->isReadyForIssuing();
    }

    /**
     * Whether the permit application can be cancelled
     *
     * @return bool
     */
    public function canBeCancelled()
    {
        return $this->isNotYetSubmitted();
    }

    /**
     * Whether the permit application is ready to be issued
     *
     * @return bool
     */
    public function isReadyForIssuing()
    {
        return $this->isFeePaid();
    }

    /**
     * Whether the permit application is currently being issued
     *
     * @return bool
     */
    public function isIssueInProgress()
    {
        return $this->status->getId() === self::STATUS_ISSUING;
    }

    /**
     * Get the organisation
     *
     * @return OrganisationEntity
     */
    public function getRelatedOrganisation()
    {
        return $this->getLicence()->getOrganisation();
    }

    /**
     * Calculates the intensity_of_use value for
     * permits requested by an ecmtPermitApplication
     */
    public function getPermitIntensityOfUse()
    {
        return $this->permitsRequired > 0 ? $this->trips / $this->permitsRequired : 0;
    }

    /**
     * Calculates the application_score value for
     * permits requested by an ecmtPermitApplication
     */
    public function getPermitApplicationScore()
    {
        $interJourneysDecValue = self::INTERNATIONAL_JOURNEYS_DECIMAL_MAP[$this->internationalJourneys->getId()];
        return $this->getPermitIntensityOfUse() * $interJourneysDecValue;
    }

    /**
     * Is there an overdue issue fee for this application?
     *
     * @return bool
     */
    public function issueFeeOverdue()
    {
        $timeToPay = new \DateInterval('P10D');
        $cutoff = new \DateTime();
        $cutoff->sub($timeToPay);

        $criteria = Criteria::create();
        $criteria->andWhere(Criteria::expr()->lte('invoicedDate', $cutoff->format(\DateTime::ISO8601)));
        $criteria->orderBy(['invoicedDate' => Criteria::DESC]);

        $matchedFees = $this->getFees()->matching($criteria);

        /**
         * @var Fee $fee
         */
        foreach ($matchedFees as $fee) {
            if ($fee->isOutstanding() && $fee->getFeeType()->isEcmtIssue()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get Latest Outstanding Ecmt Application Fee
     *
     * @return FeeEntity|null
     */
    public function getLatestOutstandingEcmtApplicationFee()
    {
        $feeTypeIds = [FeeTypeEntity::FEE_TYPE_ECMT_APP, FeeTypeEntity::FEE_TYPE_ECMT_ISSUE];
        $criteria = Criteria::create()
            ->orderBy(['invoicedDate' => Criteria::DESC]);

        /** @var Fee $fee */
        foreach ($this->getFees()->matching($criteria) as $fee) {
            if ($fee->isOutstanding()
                && in_array($fee->getFeeType()->getFeeType()->getId(), $feeTypeIds)) {
                return $fee;
            }
        }
        return null;
    }

    /**
     * Get All Outstanding Ecmt Application Fees
     *
     * @return array
     */
    public function getOutstandingFees()
    {
        $feeTypeIds = [FeeTypeEntity::FEE_TYPE_ECMT_APP, FeeTypeEntity::FEE_TYPE_ECMT_ISSUE];
        $fees = [];

        /** @var Fee $fee */
        foreach ($this->getFees() as $fee) {
            if ($fee->isOutstanding() && in_array($fee->getFeeType()->getFeeType()->getId(), $feeTypeIds)) {
                $fees[] = $fee;
            }
        }
        return $fees;
    }

    /**
     * Does ECMT Application have any outstanding Fees?
     *
     */
    public function hasOutstandingFees()
    {
        return count($this->getLatestOutstandingEcmtApplicationFee());
    }

    /**
     * Retrieves the first Irhp Permit Application linked to the Ecmt Permit Application.
     * There should only ever be one Irhp Permit Application per Ecmt Permit Application.
     *
     * @return IrhpPermitApplication
     * @throws RuntimeException
     */
    public function getFirstIrhpPermitApplication()
    {
        if ($this->irhpPermitApplications->count() != 1) {
            throw new RuntimeException(
                sprintf(
                    'This ECMT Application has none or more than one IRHP Permit Application (id: %d)',
                    $this->getId()
                )
            );
        }

        return $this->irhpPermitApplications->first();
    }

    /**
     * Return the number of permits awarded to this application. Only applicable to applications that are currently
     * under consideration
     *
     * @return int
     *
     * @throws ForbiddenException
     */
    public function getPermitsAwarded()
    {
        if (!$this->isUnderConsideration()) {
            throw new ForbiddenException(
                'This application is not in the correct state to return permits awarded ('.$this->status->getId().')'
            );
        }

        $irhpPermitApplication = $this->getFirstIrhpPermitApplication();
        return $irhpPermitApplication->countPermitsAwarded();
    }

    /**
     * Indicate whether this application is either unsuccessful, partially successful or fully successful, returning
     * one of the class constants SUCCESS_LEVEL_NONE, SUCCESS_LEVEL_PARTIAL or SUCCESS_LEVEL_FULL accordingly
     *
     * @return string
     */
    public function getSuccessLevel()
    {
        $permitsAwarded = $this->getPermitsAwarded();

        $successLevel = self::SUCCESS_LEVEL_PARTIAL;
        if ($permitsAwarded == 0) {
            $successLevel = self::SUCCESS_LEVEL_NONE;
        } elseif ($this->permitsRequired == $permitsAwarded) {
            $successLevel = self::SUCCESS_LEVEL_FULL;
        }

        return $successLevel;
    }

    /**
     * Indicate the type of notification required upon completion of scoring acceptance, returning one of the
     * NOTIFICATION_TYPE_* constants
     *
     * @return string
     */
    public function getOutcomeNotificationType()
    {
        $mappings = [
            self::SOURCE_SELFSERVE => self::NOTIFICATION_TYPE_EMAIL,
            self::SOURCE_INTERNAL => self::NOTIFICATION_TYPE_MANUAL
        ];

        return $mappings[$this->source->getId()];
    }

    /**
     *
     * Return data required for the creation of a HTML snapshot
     * @return array
     */
    public function returnSnapshotData()
    {
        $data['permitType'] = $this->getPermitType()->getDescription();
        $data['operator'] = $this->getLicence()->getOrganisation()->getName();
        $data['ref'] = $this->getApplicationRef();
        $data['licence'] = $this->getLicence()->getLicNo();
        $data['emissions'] =  (int) $this->getEmissions() === 1 ? 'Yes' : 'No';
        $data['cabotage'] = (int) $this->getCabotage() === 1 ? 'Yes' : 'No';

        $data['limitedCountries'] =  'No';
        $data['limitedCountriesList'] = null;
        if ((int) $this->getHasRestrictedCountries() === 1) {
            $data['limitedCountries'] = 'Yes';
            $countries = [];
            foreach ($this->getCountrys() as $country) {
                $countries[] = $country->getCountryDesc();
            }
            $data['limitedCountriesList'] = implode(", ", $countries);
        }

        $data['permitsRequired'] = $this->getPermitsRequired();
        $data['trips'] = $this->getTrips();
        $data['internationalJourneys'] = $this->getInternationalJourneys()->getDescription();
        $data['goods'] = $this->getSectors()->getName();

        // default to Euro 6
        $data['emissionsQuestion']
            = 'I confirm that my ECMT permits will only be used by vehicles that are environmentally compliant with '
                . 'Euro 6 emissions standards.';
        $data['emissionsDeclaration']
            = 'In the next 12 months are you transporting goods to Austria, Greece, Hungary, Italy or Russia?';

        if ($this->getWindowEmissionsCategory() === IrhpPermitWindow::EMISSIONS_CATEGORY_EURO5_REF) {
            // Euro 5
            $data['emissionsQuestion']
                = 'I confirm that my ECMT permits will only be used by vehicles that are environmentally compliant '
                    . 'with Euro 5 emissions standards as a minimum.';
            $data['emissionsDeclaration']
                = 'I confirm that I will not transport goods to, through and from Austria, Greece, Hungary, Italy '
                    . 'or Russia using this ECMT permit.';
            $data['limitedCountries'] = 'Yes';
            $data['limitedCountriesList'] = null;
        }

        return $data;
    }
}
