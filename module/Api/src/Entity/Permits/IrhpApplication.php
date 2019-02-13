<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\CancelableInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\OrganisationProviderInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\SectionableInterface;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Traits\SectionTrait;

/**
 * IrhpApplication Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="irhp_application",
 *    indexes={
 *        @ORM\Index(name="ix_irhp_application_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_irhp_application_source", columns={"source"}),
 *        @ORM\Index(name="ix_irhp_application_status", columns={"status"}),
 *        @ORM\Index(name="ix_irhp_application_irhp_permit_type_id", columns={"irhp_permit_type_id"}),
 *        @ORM\Index(name="ix_irhp_application_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_irhp_application_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class IrhpApplication extends AbstractIrhpApplication implements
    IrhpInterface,
    OrganisationProviderInterface,
    SectionableInterface,
    CancelableInterface
{
    use SectionTrait;

    const ERR_CANT_CANCEL = 'Unable to cancel this application';
    const ERR_CANT_CHECK_ANSWERS = 'Unable to check answers: the sections of the application have not been completed.';
    const ERR_CANT_MAKE_DECLARATION = 'Unable to make declaration: the sections of the application have not been completed.';
    const ERR_CANT_SUBMIT = 'This application cannot be submitted';
    const ERR_CANT_ISSUE = 'This application cannot be issued';

    const SECTIONS = [
        'licence' => [
            'validator' => 'fieldIsNotNull',
        ],
        'countries' => [
            'validator' => 'countriesPopulated',
        ],
        'permitsRequired' => [
            'validator' => 'permitsRequiredPopulated',
            'validateIf' => [
                'countries' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
            ],
        ],
        'checkedAnswers' => [
            'validator' => 'fieldIsAgreed',
            'validateIf' => [
                'licence' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                'countries' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                'permitsRequired' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
            ],
        ],
        'declaration' => [
            'validator' => 'fieldIsAgreed',
            'validateIf' => [
                'checkedAnswers' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
            ],
        ],
    ];

    /** @var int|null */
    private $storedPermitsRequired;

    /**
     * This is a custom validator for the countries field
     *
     * @param string $field field being checked
     *
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private function countriesPopulated($field)
    {
        return $this->collectionHasRecord('irhpPermitApplications');
    }

    /**
     * This is a custom validator for the permitsRequired field
     *
     * @param string $field field being checked
     *
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private function permitsRequiredPopulated($field)
    {
        /** @var IrhpPermitApplication $irhpPermitApplication */
        foreach ($this->getIrhpPermitApplications() as $irhpPermitApplication) {
            if (!$irhpPermitApplication->hasPermitsRequired()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Gets calculated values
     *
     * @return array
     */
    public function getCalculatedBundleValues()
    {
        return [
            'applicationRef' => $this->getApplicationRef(),
            'canBeCancelled' => $this->canBeCancelled(),
            'canBeSubmitted' => $this->canBeSubmitted(),
            'canBeUpdated' => $this->canBeUpdated(),
            'hasOutstandingFees' => $this->hasOutstandingFees(),
            'outstandingFeeAmount' => $this->getOutstandingFeeAmount(),
            'sectionCompletion' => $this->getSectionCompletion(),
            'hasCheckedAnswers' => $this->hasCheckedAnswers(),
            'hasMadeDeclaration' => $this->hasMadeDeclaration(),
            'isNotYetSubmitted' => $this->isNotYetSubmitted(),
            'isValid' => $this->isValid(),
            'isFeePaid' => $this->isFeePaid(),
            'isIssueInProgress' => $this->isIssueInProgress(),
            'isAwaitingFee' => $this->isAwaitingFee(),
            'isUnderConsideration' => $this->isUnderConsideration(),
            'isReadyForNoOfPermits' => $this->isReadyForNoOfPermits(),
            'isCancelled' => $this->isCancelled(),
            'canCheckAnswers' => $this->canCheckAnswers(),
            'canMakeDeclaration' => $this->canMakeDeclaration(),
            'permitsRequired' => $this->getPermitsRequired(),
            'canUpdateCountries' => $this->canUpdateCountries(),
        ];
    }

    /**
     * Get the application reference
     *
     * @return string
     */
    public function getApplicationRef()
    {
        return sprintf('%s / %d', $this->licence->getLicNo(), $this->id);
    }

    /**
     * Get the organisation
     *
     * @return Organisation
     */
    public function getRelatedOrganisation()
    {
        return $this->getLicence()->getOrganisation();
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->status->getId() === IrhpInterface::STATUS_VALID;
    }

    /**
     * @return bool
     */
    public function isNotYetSubmitted()
    {
        return $this->status->getId() === IrhpInterface::STATUS_NOT_YET_SUBMITTED;
    }

    /**
     * @return bool
     */
    public function isUnderConsideration()
    {
        return $this->status->getId() === IrhpInterface::STATUS_UNDER_CONSIDERATION;
    }

    /**
     * @return bool
     */
    public function isAwaitingFee()
    {
        return $this->status->getId() === IrhpInterface::STATUS_AWAITING_FEE;
    }

    /**
     * @return bool
     */
    public function isFeePaid()
    {
        return $this->status->getId() === IrhpInterface::STATUS_FEE_PAID;
    }

    /**
     * @return bool
     */
    public function isIssueInProgress()
    {
        return $this->status->getId() === IrhpInterface::STATUS_ISSUING;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->isNotYetSubmitted() || $this->isUnderConsideration() || $this->isAwaitingFee()
            || $this->isFeePaid();
    }

    /**
     * @return bool
     */
    public function isCancelled()
    {
        return $this->status->getId() === IrhpInterface::STATUS_CANCELLED;
    }

    /**
     * Cancel an application
     *
     * @param RefData $refData cancellation status
     *
     * @return void
     */
    public function cancel(RefData $cancelStatus)
    {
        if (!$this->canBeCancelled()) {
            throw new ForbiddenException(self::ERR_CANT_CANCEL);
        }

        $this->status = $cancelStatus;
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
     * Have the answers been checked
     *
     * @return bool
     */
    public function hasCheckedAnswers()
    {
        return $this->fieldIsAgreed('checkedAnswers');
    }

    /**
     * Whether countries can be updated
     *
     * @return bool
     */
    public function canUpdateCountries()
    {
        return $this->canBeUpdated()
            && $this->getIrhpPermitType()->getId() === IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL
            && $this->isFieldReadyToComplete('countries');
    }

    /**
     * Update checkedAnswers to true
     *
     * @throws ForbiddenException
     */
    public function updateCheckAnswers()
    {
        if (!$this->canCheckAnswers()) {
            throw new ForbiddenException(self::ERR_CANT_CHECK_ANSWERS);
        }
        return $this->checkedAnswers = true;
    }

    /**
     * Whether checkedAnswers can be updated
     *
     * @return bool
     */
    public function canCheckAnswers()
    {
        return $this->canBeUpdated() && $this->isFieldReadyToComplete('checkedAnswers');
    }

    /**
     * Have the answers been checked
     *
     * @return bool
     */
    public function hasMadeDeclaration()
    {
        return $this->fieldIsAgreed('declaration');
    }

    /**
     * Whether the application can be submitted
     *
     * @return bool
     */
    public function canBeSubmitted()
    {
        if (!$this->isNotYetSubmitted()) {
            return false;
        }

        $sections = $this->getSectionCompletion();

        if (!$sections['allCompleted']) {
            return false;
        }

        return $this->getLicence()->canMakeIrhpApplication($this->getIrhpPermitType(), $this);
    }

    /**
     * Whether the application has any outstanding fees
     *
     * @return bool
     */
    public function hasOutstandingFees()
    {
        $fee = $this->getLatestOutstandingFeeByTypes(
            [FeeTypeEntity::FEE_TYPE_IRHP_APP, FeeTypeEntity::FEE_TYPE_IRHP_ISSUE]
        );

        return $fee !== null;
    }

    /**
     * Return the latest application fee, or none if no application fee is present
     *
     * @return Fee|null
     */
    public function getLatestOutstandingApplicationFee()
    {
        return $this->getLatestOutstandingFeeByTypes([FeeTypeEntity::FEE_TYPE_IRHP_APP]);
    }

    /**
     * Return the latest issue fee, or none if no issue fee is present
     *
     * @return FeeEntity|null
     */
    public function getLatestOutstandingIssueFee()
    {
        return $this->getLatestOutstandingFeeByTypes([FeeTypeEntity::FEE_TYPE_IRHP_ISSUE]);
    }

    /**
     * Get latest outstanding fee by types
     *
     * @param array $feeTypeIds
     *
     * @return FeeEntity|null
     */
    private function getLatestOutstandingFeeByTypes($feeTypeIds)
    {
        $criteria = Criteria::create()
            ->orderBy(['invoicedDate' => Criteria::DESC]);

        /** @var FeeEntity $fee */
        foreach ($this->getFees()->matching($criteria) as $fee) {
            if ($fee->isOutstanding() && in_array($fee->getFeeType()->getFeeType()->getId(), $feeTypeIds)) {
                return $fee;
            }
        }
        return null;
    }

    /**
     * Return the amount of the outstanding fees (application + issue)
     *
     * @return float|int
     */
    public function getOutstandingFeeAmount()
    {
        $feeAmount = 0;
        $outstandingFees = $this->getOutstandingFees();

        /** @var FeeEntity $fee */
        foreach ($outstandingFees as $fee) {
            $feeAmount += $fee->getGrossAmount();
        }

        return $feeAmount;
    }

    /**
     * Get All Outstanding IRHP Application Fees
     *
     * @return array
     */
    public function getOutstandingFees()
    {
        $feeTypeIds = [FeeTypeEntity::FEE_TYPE_IRHP_APP, FeeTypeEntity::FEE_TYPE_IRHP_ISSUE];
        $fees = [];

        /** @var FeeEntity $fee */
        foreach ($this->getFees() as $fee) {
            if ($fee->isOutstanding() && in_array($fee->getFeeType()->getFeeType()->getId(), $feeTypeIds)) {
                $fees[] = $fee;
            }
        }
        return $fees;
    }

    /**
     * Returns true if the application is in a state where the number of permits can be specified against each
     * relevant stock (i.e. one or more instances of IrhpPermitApplication have already been created against this
     * IrhpApplication)
     *
     * @return bool
     */
    public function isReadyForNoOfPermits()
    {
        $canBeUpdated = $this->canBeUpdated();
        $hasIrhpPermitApplications = count($this->irhpPermitApplications) > 0;

        return $canBeUpdated && $hasIrhpPermitApplications;
    }

    /**
     * Whether the application can be updated
     *
     * @return bool
     */
    public function canBeUpdated()
    {
        return $this->isNotYetSubmitted();
    }

    /**
     * Reset the checked answers and declaration sections to a value representing 'not completed'
     */
    public function resetCheckAnswersAndDeclaration()
    {
        if ($this->canBeUpdated()) {
            $this->declaration = false;
            $this->checkedAnswers = false;
        }
    }

    /**
     * Reset application answers - sets properties to null, or calls individual update methods in more important cases
     */
    public function clearAnswers()
    {
        if ($this->canBeUpdated()) {
            $this->irhpPermitApplications = new ArrayCollection();
            $this->resetCheckAnswersAndDeclaration();
        }
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
     * @param RefData $source
     * @param RefData $status
     * @param RefData $irhpPermitType
     * @param RefData $licence
     * @param string|null $dateReceived
     * @return IrhpApplication
     */
    public static function createNew(
        RefData $source,
        RefData $status,
        IrhpPermitType $irhpPermitType,
        Licence $licence,
        string $dateReceived = null
    ) {
        $irhpApplication = new self();
        $irhpApplication->source = $source;
        $irhpApplication->status = $status;
        $irhpApplication->irhpPermitType = $irhpPermitType;
        $irhpApplication->licence = $licence;
        $irhpApplication->dateReceived = static::processDate($dateReceived);
        return $irhpApplication;
    }

    /**
     * Mark Declaration field as true
     *
     * @return bool
     * @throws ForbiddenException
     */
    public function makeDeclaration()
    {
        if (!$this->canMakeDeclaration()) {
            throw new ForbiddenException(self::ERR_CANT_MAKE_DECLARATION);
        }
        return $this->declaration = true;
    }

    /**
     * Whether declaration can be be updated
     *
     * @return bool
     */
    public function canMakeDeclaration()
    {
        return $this->canBeUpdated() && $this->isFieldReadyToComplete('declaration');
    }

    /**
     * Submit Application - Placeholder method to allow Declaration Page redirects to work for testing.
     *
     * @throws ForbiddenException
     */
    public function submit(RefData $submitStatus)
    {
        if (!$this->canBeSubmitted()) {
            throw new ForbiddenException(self::ERR_CANT_SUBMIT);
        }

        $this->proceedToIssuing($submitStatus);
    }

    /**
     * Gets the total number of Permits Required for the IRHP Permit Applications
     *
     * @return integer
     */
    public function getPermitsRequired()
    {
        $applications = $this->irhpPermitApplications;
        $total = 0;

        foreach ($applications as $app) {
            $total += is_null($app->getPermitsRequired()) ? 0 : $app->getPermitsRequired();
        }

        return $total;
    }

    /**
     * Calculates and stores the total number of permits required by this application. Intended to be used in
     * conjunction with hasPermitsRequiredChanged
     */
    public function storePermitsRequired()
    {
        $this->storedPermitsRequired = $this->getPermitsRequired();
    }

    /**
     * Whether the total permits required has changed since the last call to storePermitsRequired. Can be used
     * to determine whether the issue fee needs to be regenerated
     *
     * @return bool
     */
    public function hasPermitsRequiredChanged()
    {
        return $this->getPermitsRequired() != $this->storedPermitsRequired;
    }

    /**
     * Whether the issue fee can be created or replaced
     *
     * @return bool
     */
    public function canCreateOrReplaceIssueFee()
    {
        return $this->isNotYetSubmitted();
    }

    /**
     * Whether the application fee can be created or replaced
     *
     * @return bool
     */
    public function canCreateOrReplaceApplicationFee()
    {
        return $this->isNotYetSubmitted();
    }

    /**
     * Updates date receive for application
     * @param string $dateReceived application received date
     */
    public function updateDateReceived($dateReceived)
    {
        $this->dateReceived = $this->processDate($dateReceived);
    }

    /**
     * Whether we're able to issue permits for this application
     *
     * @return bool
     */
    public function isReadyForIssuing()
    {
        return !$this->hasOutstandingFees();
    }

    /**
     * Proceeds the application from not yet submitted status to issuing status
     *
     * @param RefData $issuingStatus
     *
     * @throws ForbiddenException
     */
    public function proceedToIssuing(RefData $issuingStatus)
    {
        if (!$this->isReadyForIssuing()) {
            throw new ForbiddenException(self::ERR_CANT_ISSUE);
        }

        $this->status = $issuingStatus;
    }

    /**
     * Proceeds the application from issuing status to valid status
     *
     * @param RefData $validStatus
     *
     * @throws ForbiddenException
     */
    public function proceedToValid(RefData $validStatus)
    {
        if (!$this->isIssueInProgress()) {
            throw new ForbiddenException('This application is not in the correct state to proceed to valid ('.$this->status->getId().')');
        }

        $this->status = $validStatus;
    }
}
