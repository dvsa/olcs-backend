<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtSuccessful;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtUnsuccessful;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtPartSuccessful;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Entity\CancelableInterface;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\LicenceProviderInterface;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\OrganisationProviderInterface;
use Dvsa\Olcs\Api\Entity\Permits\Traits\ApplicationAcceptConsts;
use Dvsa\Olcs\Api\Entity\Permits\Traits\CandidatePermitCreationTrait;
use Dvsa\Olcs\Api\Entity\Traits\PermitAppReviveFromUnsuccessfulTrait;
use Dvsa\Olcs\Api\Entity\Traits\PermitAppReviveFromWithdrawnTrait;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Task\Task;
use Dvsa\Olcs\Api\Entity\Traits\FetchPermitAppSubmissionTaskTrait;
use Dvsa\Olcs\Api\Entity\Traits\TieredProductReference;
use Dvsa\Olcs\Api\Entity\WithdrawableInterface;
use Dvsa\Olcs\Api\Service\Permits\Checkable\CheckableApplicationInterface;

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
class EcmtPermitApplication extends AbstractEcmtPermitApplication implements
    OrganisationProviderInterface,
    CancelableInterface,
    WithdrawableInterface,
    LicenceProviderInterface,
    IrhpInterface,
    CheckableApplicationInterface
{
    use TieredProductReference,
        CandidatePermitCreationTrait,
        FetchPermitAppSubmissionTaskTrait,
        PermitAppReviveFromWithdrawnTrait,
        PermitAppReviveFromUnsuccessfulTrait;

    const NOTIFICATION_TYPE_EMAIL = 'notification_type_email';
    const NOTIFICATION_TYPE_MANUAL = 'notification_type_manual';

    const PERMIT_TYPE = 'permit_ecmt';
    const PERMIT_TEMPLATE_NAME = 'IRHP_PERMIT_ECMT';
    const PERMIT_COVERING_LETTER_TEMPLATE_NAME = 'IRHP_PERMIT_ECMT_COVERING_LETTER';

    const SECTION_COMPLETION_CANNOT_START = 'ecmt_section_sts_csy';
    const SECTION_COMPLETION_NOT_STARTED = 'ecmt_section_sts_nys';
    const SECTION_COMPLETION_COMPLETED = 'ecmt_section_sts_com';

    /**
     * @todo this needs to be much more robust, not least because how we store certain data is going to change
     */
    const SECTIONS = [
        'licence' => 'fieldIsNotNull',
        'emissions' => 'fieldIsAgreed',
        'cabotage' => 'fieldIsAgreed',
        'roadworthiness' => 'fieldIsAgreed',
        'internationalJourneys' => 'fieldIsNotNull',
        'trips' => 'fieldIsInt',
        'permitsRequired' => 'oneEmissionCatRequested',
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
     * @param int|null $roadworthiness
     * @param int|null $declaration
     * @param int|null $emissions
     * @param int|null $requiredEuro5
     * @param int|null $requiredEuro6
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
        int $roadworthiness = null,
        int $declaration = null,
        int $emissions = null,
        int $requiredEuro5 = null,
        int $requiredEuro6 = null,
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
        $ecmtPermitApplication->roadworthiness = $roadworthiness;
        $ecmtPermitApplication->declaration = $declaration;
        $ecmtPermitApplication->emissions = $emissions;
        $ecmtPermitApplication->requiredEuro5 = $requiredEuro5;
        $ecmtPermitApplication->requiredEuro6 = $requiredEuro6;
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
     * @param int|null $requiredEuro5
     * @param int|null $requiredEuro6
     * @param int|null $trips
     * @param RefData $internationalJourneys
     * @param string|null $dateReceived
     * @param int|null $roadworthiness
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
        int $requiredEuro5 = null,
        int $requiredEuro6 = null,
        int $trips = null,
        RefData $internationalJourneys = null,
        string $dateReceived = null,
        int $roadworthiness = null
    ) {
        $this->permitType = $permitType ?? $this->permitType;
        $this->licence = $licence;
        $this->sectors = $sectors;
        $this->updateCountrys($countrys);
        $this->cabotage = $cabotage;
        $this->checkedAnswers = $declaration; //auto updated alongside declaration for internal apps
        $this->declaration = $declaration;
        $this->emissions = $emissions;
        $this->requiredEuro5 = $requiredEuro5;
        $this->requiredEuro6 = $requiredEuro6;
        $this->trips = $trips;
        $this->internationalJourneys = $internationalJourneys;
        $this->dateReceived = $this->processDate($dateReceived);
        $this->roadworthiness = $roadworthiness;

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

    public function withdraw(RefData $withdrawStatus, RefData $withdrawReason): void
    {
        if (!$this->canBeWithdrawn()) {
            throw new ForbiddenException(WithdrawableInterface::ERR_CANT_WITHDRAW);
        }

        $this->status = $withdrawStatus;
        $this->withdrawReason = $withdrawReason;
        $this->withdrawnDate = new \DateTime();
    }

    public function decline(RefData $declineStatus, RefData $withdrawReason)
    {
        if (!$this->canBeDeclined()) {
            throw new ForbiddenException('This application is not allowed to be declined');
        }

        $this->status = $declineStatus;
        $this->withdrawReason = $withdrawReason;
        $this->withdrawnDate = new \DateTime();
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
        $this->cancellationDate = new \DateTime();
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
     * Changes the status to expired
     *
     * @param RefData $expireStatus
     *
     * @throws ForbiddenException
     */
    public function expire(RefData $expireStatus)
    {
        if (!$this->canBeExpired()) {
            $irhpPermitApplication = $this->getIrhpPermitApplications()->first();
            throw new ForbiddenException(
                sprintf(
                    'This application can not be expired. (No of valid permits: %s)',
                    $irhpPermitApplication->countValidPermits()
                )
            );
        }
        $this->status = $expireStatus;
        $this->expiryDate = new DateTime();
    }

    /**
     * Gets calculated values
     *
     * @return array
     */
    public function getCalculatedBundleValues()
    {
        $sectionCompletion = $this->getSectionCompletion(self::SECTIONS);

        try {
            $totalPermitsRequired = $this->calculateTotalPermitsRequired();
        } catch (RuntimeException $e) {
            $totalPermitsRequired = null;
        }

        return [
            'applicationRef' => $this->getApplicationRef(),
            'canBeCancelled' => $this->canBeCancelled(),
            'canBeSubmitted' => $this->canBeSubmitted(),
            'canBeRevivedFromUnsuccessful' => $this->canBeRevivedFromUnsuccessful(),
            'canBeRevivedFromWithdrawn' => $this->canBeRevivedFromWithdrawn(),
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
            'totalPermitsRequired' => $totalPermitsRequired
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
        $this->roadworthiness = null;
        $this->trips = null;
        $this->internationalJourneys = null;
        $this->sectors = null;
        $this->updatePermitsRequired(null, null);
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
     * Updates roadworthiness
     *
     * @param bool $roadworthiness Roadworthiness
     *
     * @return void
     */
    public function updateRoadworthiness($roadworthiness)
    {
        $this->roadworthiness = $roadworthiness;
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
     * @param int|null $euro5
     * @param int|null $euro6
     */
    public function updatePermitsRequired(?int $euro5, ?int $euro6)
    {
        $this->requiredEuro5 = $euro5;
        $this->requiredEuro6 = $euro6;
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
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function fieldIsNotNull($field)
    {
        return $this->$field !== null;
    }

    /**
     * @param string $field field being checked
     *
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
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
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function countrysPopulated($field)
    {
        if ($this->hasRestrictedCountries === false) {
            return true;
        }

        return $this->collectionHasRecord($field);
    }

    /**
     * This is a custom validator for the 2 permits required fields
     *
     * @param string $field field being checked
     *
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function oneEmissionCatRequested($field)
    {
        try {
            return $this->calculateTotalPermitsRequired();
        } catch (RuntimeException $e) {
            return false;
        }
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
        return in_array($this->status->getId(), IrhpInterface::ACTIVE_STATUSES);
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
    public function isWithdrawn(): bool
    {
        return $this->status->getId() === IrhpInterface::STATUS_WITHDRAWN;
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
     * Whether the permit application can be expired
     *
     * @return bool
     */
    public function canBeExpired()
    {
        $irhpPermitApplication = $this->getIrhpPermitApplications()->first();
        if ($this->isValid() && !$irhpPermitApplication->hasValidPermits()) {
            return true;
        }
        return false;
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

        return $this->licence->canMakeEcmtApplication($this->getAssociatedStock(), $this);
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
     * Whether the permit application can be withdrawn optional withdraw reason
     *
     * @param RefData $reason reason application is being withdrawn
     *
     * @return bool
     */
    public function canBeWithdrawn(?RefData $reason = null): bool
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
     * Get the related licence
     *
     * @return Licence
     */
    public function getRelatedLicence(): Licence
    {
        return $this->licence;
    }

    /**
     * Calculates the intensity_of_use value for permits requested by an ecmtPermitApplication
     *
     * @param string $emissionsCategoryId|null
     *
     * return float
     *
     * @throws RuntimeException
     */
    public function getPermitIntensityOfUse($emissionsCategoryId = null)
    {
        if ($emissionsCategoryId == RefData::EMISSIONS_CATEGORY_EURO5_REF) {
            $numberOfPermits = $this->getRequiredEuro5();
        } elseif ($emissionsCategoryId == RefData::EMISSIONS_CATEGORY_EURO6_REF) {
            $numberOfPermits = $this->getRequiredEuro6();
        } elseif (is_null($emissionsCategoryId)) {
            $numberOfPermits = $this->calculateTotalPermitsRequired();
        } else {
            throw new RuntimeException(
                'Unexpected emissionsCategoryId parameter for getPermitIntensityOfUse: ' . $emissionsCategoryId
            );
        }

        return $this->calculatePermitIntensityOfUse($this->trips, $numberOfPermits);
    }

    /**
     * Calculates the application_score value for permits requested by an ecmtPermitApplication
     *
     * @param string $emissionsCategoryId|null
     *
     * @return float
     */
    public function getPermitApplicationScore($emissionsCategoryId = null)
    {
        return $this->calculatePermitApplicationScore(
            $this->getPermitIntensityOfUse($emissionsCategoryId),
            $this->internationalJourneys->getId()
        );
    }

    /**
     * Gets fees over a certain number of days old
     *
     * @param int $days fees invoiced over a certain number of days ago
     *
     * @return ArrayCollection
     */
    public function getFeesByAge(int $days = 10): ArrayCollection
    {
        $cutoff = new \DateTime('-' . $days . ' weekdays');

        $criteria = Criteria::create();
        $criteria->andWhere(Criteria::expr()->lte('invoicedDate', $cutoff->format(\DateTime::ISO8601)));
        $criteria->orderBy(['invoicedDate' => Criteria::DESC]);

        return $this->getFees()->matching($criteria);
    }

    /**
     * Is there an overdue issue fee for this application?
     * @todo paramatarise cutoff number of days https://jira.i-env.net/browse/OLCS-21979
     *
     * @return bool
     */
    public function issueFeeOverdue()
    {
        $matchedFees = $this->getFeesByAge();

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
     * @return Fee|null
     */
    public function getLatestOutstandingEcmtApplicationFee()
    {
        $feeTypeIds = [FeeType::FEE_TYPE_ECMT_APP, FeeType::FEE_TYPE_ECMT_ISSUE];
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
    public function getOutstandingFees(): array
    {
        $feeTypeIds = [FeeType::FEE_TYPE_ECMT_APP, FeeType::FEE_TYPE_ECMT_ISSUE];
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
        return !is_null($this->getLatestOutstandingEcmtApplicationFee());
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
     * Get the associated stock for this application
     *
     * @return IrhpPermitStock
     *
     * @throws RuntimeException
     */
    public function getAssociatedStock(): IrhpPermitStock
    {
        return $this->getFirstIrhpPermitApplication()->getIrhpPermitWindow()->getIrhpPermitStock();
    }

    /**
     * Get question and answer data - note this is a bit of a fudge, future permit types can get most of this from DB
     *
     * @return array
     */
    public function getQuestionAnswerData()
    {
        $limitedCountriesAnswer = ['No'];

        if ($this->hasRestrictedCountries) {
            $limitedCountriesAnswer = ['Yes'];
            $countries = [];

            /** @var Country $country */
            foreach ($this->countrys as $country) {
                $countries[] = $country->getCountryDesc();
            }

            $limitedCountriesAnswer[] = implode(', ', $countries);
        }

        $year = $this->getFirstIrhpPermitApplication()
            ->getIrhpPermitWindow()
            ->getIrhpPermitStock()
            ->getValidityYear();
        $permitsRequiredAnswer = ['Permits for '  . $year];

        if ($this->requiredEuro5) {
            $permitsRequiredAnswer[] = $this->requiredEuro5 . ' permits for Euro 5 minimum emission standard';
        }

        if ($this->requiredEuro6) {
            $permitsRequiredAnswer[] = $this->requiredEuro6 . ' permits for Euro 6 minimum emission standard';
        }

        $data = [
            [
                'question' => 'permits.check-answers.page.question.licence',
                'answer' =>  $this->licence->getLicNo(),
                'questionType' => Question::QUESTION_TYPE_STRING,
            ],
            [
                'question' => 'permits.form.cabotage.label',
                'answer' =>  $this->cabotage,
                'questionType' => Question::QUESTION_TYPE_BOOLEAN,
            ],
            [
                'question' => 'permits.page.roadworthiness.question',
                'answer' =>  $this->roadworthiness,
                'questionType' => Question::QUESTION_TYPE_BOOLEAN,
            ],
            [
                'question' => 'permits.page.restricted-countries.question',
                'answer' => $limitedCountriesAnswer,
                'questionType' => Question::QUESTION_TYPE_STRING,
            ],
            [
                'question' => 'permits.form.euro-emissions.label',
                'answer' =>  $this->emissions,
                'questionType' => Question::QUESTION_TYPE_BOOLEAN,
            ],
            [
                'question' => 'permits.page.permits.required.question',
                'answer' => $permitsRequiredAnswer,
                'questionType' => Question::QUESTION_TYPE_STRING,
            ],
            [
                'question' => 'permits.page.number-of-trips.question',
                'answer' => $this->trips,
                'questionType' => Question::QUESTION_TYPE_INTEGER,
            ],
            [
                'question' => 'permits.page.international.journey.question',
                'answer' => $this->internationalJourneys->getId(),
                'questionType' => Question::QUESTION_TYPE_STRING,
            ],
            [
                'question' => 'permits.page.sectors.question',
                'answer' => $this->sectors->getName(),
                'questionType' => Question::QUESTION_TYPE_STRING,
            ],
        ];

        return $data;
    }

    /**
     * @return int
     * @throws RuntimeException
     */
    public function calculateTotalPermitsRequired()
    {
        if (is_null($this->requiredEuro5) || is_null($this->requiredEuro6)) {
            throw new RuntimeException('This ECMT Application has not had number of required permits set yet.');
        }
        return $this->requiredEuro5 + $this->requiredEuro6;
    }

    /**
     * Return the entity name in camel case
     *
     * @return string
     */
    public function getCamelCaseEntityName()
    {
        return 'ecmtPermitApplication';
    }

    /**
     * Return an array of mappings between success levels and email commands
     *
     * @return array
     */
    public function getEmailCommandLookup()
    {
        return [
            ApplicationAcceptConsts::SUCCESS_LEVEL_NONE => SendEcmtUnsuccessful::class,
            ApplicationAcceptConsts::SUCCESS_LEVEL_PARTIAL => SendEcmtPartSuccessful::class,
            ApplicationAcceptConsts::SUCCESS_LEVEL_FULL => SendEcmtSuccessful::class
        ];
    }

    /**
     * Return the product reference to be used for the issue fee
     *
     * @return string
     */
    public function getIssueFeeProductReference()
    {
        return $this->getProductReferenceForTier();
    }

    /**
     * Update the checked value for this application
     *
     * @param bool $checked
     */
    public function updateChecked($checked)
    {
        $this->checked = $checked;
    }

    /**
     * Get the description associated with the task to be created on application submission
     *
     * @return string
     */
    public function getSubmissionTaskDescription()
    {
        return Task::TASK_DESCRIPTION_ANNUAL_ECMT_RECEIVED;
    }

    /**
     * Whether this application needs to be manually checked by a case worker before permits are allocated
     *
     * @return bool
     */
    public function requiresPreAllocationCheck()
    {
        return true;
    }
}
