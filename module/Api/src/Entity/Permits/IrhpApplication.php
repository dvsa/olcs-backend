<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\CancelableInterface;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationPath;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\LicenceProviderInterface;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\OrganisationProviderInterface;
use Dvsa\Olcs\Api\Entity\SectionableInterface;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Traits\SectionTrait;
use RuntimeException;

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
    LicenceProviderInterface,
    SectionableInterface,
    CancelableInterface
{
    use SectionTrait;

    const ERR_CANT_CANCEL = 'Unable to cancel this application';
    const ERR_CANT_CHECK_ANSWERS = 'Unable to check answers: the sections of the application have not been completed.';
    const ERR_CANT_MAKE_DECLARATION = 'Unable to make declaration: the sections of the application have not been completed.';
    const ERR_CANT_SUBMIT = 'This application cannot be submitted';
    const ERR_CANT_ISSUE = 'This application cannot be issued';
    const ERR_ONLY_SUPPORTS_BILATERAL = 'This method only supports bilateral applications';

    const SECTIONS = [
        IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM => [
            'licence' => [
                'validator' => 'fieldIsNotNull',
            ],
            'emissions' => [
                'validator' => 'emissionsPopulated',
            ],
            'permitsRequired' => [
                'validator' => 'permitsRequiredPopulated',
                'validateIf' => [
                    'emissions' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                ],
            ],
            'checkedAnswers' => [
                'validator' => 'fieldIsAgreed',
                'validateIf' => [
                    'licence' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    'emissions' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    'permitsRequired' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                ],
            ],
            'declaration' => [
                'validator' => 'fieldIsAgreed',
                'validateIf' => [
                    'checkedAnswers' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                ],
            ],
        ],
        IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL => [
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
        ],
        IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL => [
            'licence' => [
                'validator' => 'fieldIsNotNull',
            ],
            'permitsRequired' => [
                'validator' => 'permitsRequiredPopulated',
            ],
            'checkedAnswers' => [
                'validator' => 'fieldIsAgreed',
                'validateIf' => [
                    'licence' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    'permitsRequired' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                ],
            ],
            'declaration' => [
                'validator' => 'fieldIsAgreed',
                'validateIf' => [
                    'checkedAnswers' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                ],
            ],
        ],
    ];

    /** @var array */
    private $storedFeesRequired;

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
     * This is a custom validator for the emissions field
     *
     * @param string $field field being checked
     *
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private function emissionsPopulated($field)
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
        if ($this->getIrhpPermitApplications()->isEmpty()) {
            return false;
        }

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
            'isBilateral' => $this->isBilateral(),
            'isMultilateral' => $this->isMultilateral(),
            'canCheckAnswers' => $this->canCheckAnswers(),
            'canMakeDeclaration' => $this->canMakeDeclaration(),
            'permitsRequired' => $this->getPermitsRequired(),
            'canUpdateCountries' => $this->canUpdateCountries(),
        ];
    }

    /**
     * Get question and answer data
     *
     * @return array
     */
    public function getQuestionAnswerData(): array
    {
        // kept for backward compatibility only
        if ($this->isBilateral()) {
            return $this->getQuestionAnswerDataBilateral();
        } elseif ($this->isMultilateral()) {
            return $this->getQuestionAnswerDataMultilateral();
        }

        // the Q&A solution
        $activeApplicationPath = $this->getActiveApplicationPath();

        if (!isset($activeApplicationPath)) {
            return [];
        }

        // licence
        $answer = $this->licence->getLicNo();
        $status = !empty($answer)
            ? SectionableInterface::SECTION_COMPLETION_COMPLETED
            : SectionableInterface::SECTION_COMPLETION_NOT_STARTED;
        $data = [
            [
                'section' => 'licence',
                'slug' => 'custom-licence',
                'question' => 'section.name.application/licence',
                'answer' =>  $answer,
                'status' => $status,
            ]
        ];
        $previousQuestionStatus = $status;

        // list of defined steps
        foreach ($activeApplicationPath->getApplicationSteps() as $applicationStep) {
            $question = $applicationStep->getQuestion();
            $answer = null;
            $status = SectionableInterface::SECTION_COMPLETION_CANNOT_START;

            if ($previousQuestionStatus === SectionableInterface::SECTION_COMPLETION_COMPLETED) {
                $answer = $this->getAnswer($applicationStep);

                $status = isset($answer)
                    ? SectionableInterface::SECTION_COMPLETION_COMPLETED
                    : SectionableInterface::SECTION_COMPLETION_NOT_STARTED;
            }

            $data[] = [
                'section' => $question->getSlug(),
                'slug' => $question->getSlug(),
                'question' => $question->getActiveQuestionText($this->getApplicationPathLockedOn())->getQuestionKey(),
                'answer' => $answer,
                'status' => $status,
            ];
            $previousQuestionStatus = $status;
        }

        // checked answers
        $answer = null;
        $status = SectionableInterface::SECTION_COMPLETION_CANNOT_START;

        if ($previousQuestionStatus === SectionableInterface::SECTION_COMPLETION_COMPLETED) {
            $answer = $this->getCheckedAnswers();
            $status = !empty($answer)
                ? SectionableInterface::SECTION_COMPLETION_COMPLETED
                : SectionableInterface::SECTION_COMPLETION_NOT_STARTED;
        }

        $data[] = [
            'section' => 'checkedAnswers',
            'slug' => 'custom-check-answers',
            'question' => 'section.name.application/check-answers',
            'answer' => $answer,
            'status' => $status,
        ];
        $previousQuestionStatus = $status;

        // declaration
        $answer = null;
        $status = SectionableInterface::SECTION_COMPLETION_CANNOT_START;

        if ($previousQuestionStatus === SectionableInterface::SECTION_COMPLETION_COMPLETED) {
            $answer = $this->getDeclaration();
            $status = !empty($answer)
                ? SectionableInterface::SECTION_COMPLETION_COMPLETED
                : SectionableInterface::SECTION_COMPLETION_NOT_STARTED;
        }

        $data[] = [
            'section' => 'declaration',
            'slug' => 'custom-declaration',
            'question' => 'section.name.application/declaration',
            'answer' => $answer,
            'status' => $status,
        ];

        return $data;
    }

    /**
     * Get an answer to the given application step
     *
     * @return mix|null
     */
    private function getAnswer(ApplicationStep $applicationStep)
    {
        $question = $applicationStep->getQuestion();

        if ($question->isCustom()) {
            // TODO - OLCS-23788 - custom handling to be added here
            return null;
        }

        // standard Q&A
        $activeQuestionText = $question->getActiveQuestionText($this->getApplicationPathLockedOn());

        if (!isset($activeQuestionText)) {
            return null;
        }

        // answers are indexed by question_text_id
        $answer = $this->getAnswers()->get($activeQuestionText->getId());

        if (!isset($answer)) {
            return null;
        }

        return $answer->getValue();
    }

    /**
     * Get an answer to the given application step
     *
     * @return mix|null
     */
    public function getAnswer(ApplicationStep $applicationStep)
    {
        $question = $applicationStep->getQuestion();

        if ($question->isCustom()) {
            // TODO - OLCS-23788 - custom handling to be added here
            return null;
        }

        // standard Q&A
        $activeQuestionText = $question->getActiveQuestionText($this->getApplicationPathLockedOn());

        if (!isset($activeQuestionText)) {
            return null;
        }

        // answers are indexed by question_text_id
        $answer = $this->getAnswers()->get($activeQuestionText->getId());

        if (!isset($answer)) {
            return null;
        }

        return $answer->getValue();
    }

    /**
     * Get question and answer data for a bilateral application
     *
     * @return array
     */
    private function getQuestionAnswerDataBilateral(): array
    {
        $numberOfPermits = [];
        $countriesArray = [];

        /** @var IrhpPermitApplication $irhpPermitApplication */
        foreach ($this->irhpPermitApplications as $irhpPermitApplication) {
            $permitsRequired = $irhpPermitApplication->getPermitsRequired();

            //if the permits required hasn't been filled in at all, we skip (although we do show zeros)
            if ($permitsRequired === null) {
                continue;
            }

            $stock = $irhpPermitApplication->getIrhpPermitWindow()->getIrhpPermitStock();
            $country = $stock->getCountry()->getCountryDesc();
            $year = $stock->getValidTo(true)->format('Y');

            $numberOfPermits[$country][$year] = $permitsRequired;
            $countriesArray[$country] = $country;
        }

        $data = [
            [
                'question' => 'permits.check-answers.page.question.licence',
                'answer' =>  $this->licence->getLicNo(),
            ],
            [
                'question' => 'permits.irhp.countries.transporting',
                'answer' =>  implode(', ', $countriesArray),
            ],
            [
                'question' => 'permits.snapshot.number.required',
                'answer' =>  $this->getPermitsRequired(),
            ],
        ];

        foreach ($numberOfPermits as $country => $years) {
            foreach ($years as $year => $permitsRequired) {
                $data[] = [
                    'question' => sprintf('%s for %d', $country, $year),
                    'answer' => $permitsRequired
                ];
            }
        }

        return $data;
    }

    /**
     * Get question and answer data for a multilateral application
     *
     * @return array
     */
    private function getQuestionAnswerDataMultilateral(): array
    {
        $data = [
            [
                'question' => 'permits.check-answers.page.question.licence',
                'answer' =>  $this->licence->getLicNo(),
            ],
            [
                'question' => 'permits.snapshot.number.required',
                'answer' =>  $this->getPermitsRequired(),
            ],
        ];

        /** @var IrhpPermitApplication $irhpPermitApplication */
        foreach ($this->irhpPermitApplications as $irhpPermitApplication) {
            $permitsRequired = $irhpPermitApplication->getPermitsRequired();

            //if the permits required hasn't been filled in at all, we skip (although we do show zeros)
            if ($permitsRequired === null) {
                continue;
            }

            $year = $irhpPermitApplication->getIrhpPermitWindow()
                ->getIrhpPermitStock()
                ->getValidTo(true)
                ->format('Y');

            $data[] = [
                'question' => sprintf('For %d', $year),
                'answer' => $permitsRequired
            ];
        }

        return $data;
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
     * Get the related licence
     *
     * @return Licence
     */
    public function getRelatedLicence(): Licence
    {
        return $this->licence;
    }

    /**
     * Whether the application is for a bilateral permit type
     *
     * @return bool
     */
    public function isBilateral(): bool
    {
        return $this->irhpPermitType->isBilateral();
    }

    /**
     * Whether the application is for a multilateral permit type
     *
     * @return bool
     */
    public function isMultilateral(): bool
    {
        return $this->irhpPermitType->isMultilateral();
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
        $this->cancellationDate = new \DateTime();
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
     * Get a list of outstanding application fees relating to this application
     *
     * @return array
     */
    public function getOutstandingApplicationFees()
    {
        return $this->getOutstandingFeesByType(FeeTypeEntity::FEE_TYPE_IRHP_APP);
    }

    /**
     * Get a list of outstanding issue fees relating to this application
     *
     * @return array
     */
    public function getOutstandingIssueFees()
    {
        return $this->getOutstandingFeesByType(FeeTypeEntity::FEE_TYPE_IRHP_ISSUE);
    }

    /**
     * Get outstanding fees by types
     *
     * @param int $feeTypeId
     *
     * @return array
     */
    private function getOutstandingFeesByType($feeTypeId)
    {
        $fees = [];
        foreach ($this->getFees() as $fee) {
            if ($fee->isOutstanding() && $fee->getFeeType()->getFeeType()->getId() == $feeTypeId) {
                $fees[] = $fee;
            }
        }

        return $fees;
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

        /** @var IrhpPermitApplication $app */
        foreach ($applications as $app) {
            $permitsRequired = $app->getPermitsRequired();
            $total += is_null($permitsRequired) ? 0 : $permitsRequired;
        }

        return $total;
    }

    /**
     * Calculates and stores the total number of permits required by this application. Intended to be used in
     * conjunction with hasPermitsRequiredChanged
     */
    public function storeFeesRequired()
    {
        $this->storedFeesRequired = $this->getSerialisedFeeProductRefsAndQuantities();
    }

    /**
     * Whether the total permits required has changed since the last call to storePermitsRequired. Can be used
     * to determine whether the issue fee needs to be regenerated
     *
     * @return bool
     */
    public function haveFeesRequiredChanged()
    {
        if (is_null($this->storedFeesRequired)) {
            throw new RuntimeException('storeFeesRequired must be called before haveFeesRequiredChanged');
        }

        return $this->getSerialisedFeeProductRefsAndQuantities() != $this->storedFeesRequired;
    }

    /**
     * Return a json encoded representation of all applicable fee product references and associated quantities, sorted
     * by product reference
     *
     * @return string
     */
    private function getSerialisedFeeProductRefsAndQuantities()
    {
        $productRefsAndQuantities = array_merge(
            $this->getApplicationFeeProductRefsAndQuantities(),
            $this->getIssueFeeProductRefsAndQuantities()
        );

        ksort($productRefsAndQuantities);
        return json_encode($productRefsAndQuantities);
    }

    /**
     * Gets the application fee product reference for this application
     * Applicable only to bilateral and multilateral
     *
     * @return array
     *
     * @throws ForbiddenException if the permit type is unsupported
     */
    public function getApplicationFeeProductReference()
    {
        $mappings = [
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL
                => FeeTypeEntity::FEE_TYPE_IRHP_APP_BILATERAL_PRODUCT_REF,
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL
                => FeeTypeEntity::FEE_TYPE_IRHP_APP_MULTILATERAL_PRODUCT_REF,
        ];

        $irhpPermitTypeId = $this->getIrhpPermitType()->getId();
        if (!isset($mappings[$irhpPermitTypeId])) {
            throw new ForbiddenException(
                'No application fee product reference available for permit type ' . $irhpPermitTypeId
            );
        }

        return $mappings[$irhpPermitTypeId];
    }

    /**
     * Gets a key/value pair array representing application fee product references and quantities for this application
     *
     * @return array
     */
    public function getApplicationFeeProductRefsAndQuantities()
    {
        return [
            $this->getApplicationFeeProductReference() => $this->getPermitsRequired()
        ];
    }

    /**
     * Gets the total fee per permit including application fee and issue fee
     * Applicable only to bilateral and multilateral
     *
     * @param FeeTypeEntity $applicationFeeType
     * @param FeeTypeEntity $issueFeeType
     *
     * @return int
     *
     * @throws ForbiddenException if the permit type is unsupported
     */
    public function getFeePerPermit(FeeTypeEntity $applicationFeeType, FeeTypeEntity $issueFeeType)
    {
        $permittedPermitTypeIds = [
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL,
        ];

        $irhpPermitTypeId = $this->getIrhpPermitType()->getId();

        if (!in_array($irhpPermitTypeId, $permittedPermitTypeIds)) {
            throw new ForbiddenException(
                'Cannot get fee per permit for irhp permit type ' . $irhpPermitTypeId
            );
        }

        return $applicationFeeType->getFixedValue() + $issueFeeType->getFixedValue();
    }

    /**
     * Gets a key/value pair array representing issue fee product references and quantities for this application
     *
     * @return array
     *
     * @throws ForbiddenException if the permit type is unsupported
     */
    public function getIssueFeeProductRefsAndQuantities()
    {
        $cumulativeProductRefsAndQuantities = [];

        $irhpPermitApplications = $this->getIrhpPermitApplications();
        foreach ($irhpPermitApplications as $irhpPermitApplication) {
            $productRef = $irhpPermitApplication->getIssueFeeProductReference();
            $quantity = $irhpPermitApplication->getPermitsRequired();

            if ($quantity > 0) {
                if (!isset($cumulativeProductRefsAndQuantities[$productRef])) {
                    $cumulativeProductRefsAndQuantities[$productRef] = 0;
                }
                $cumulativeProductRefsAndQuantities[$productRef] += $quantity;
            }
        }

        return $cumulativeProductRefsAndQuantities;
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
     * Whether the issue fees can be created or replaced
     *
     * @return bool
     */
    public function canCreateOrReplaceIssueFee()
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

    /**
     * Get the application path locked on datetime
     *
     * @return \DateTime
     */
    public function getApplicationPathLockedOn()
    {
        // the application path locked at the time when the application was created
        return $this->getCreatedOn(true);
    }

    /**
     * Get the active application path
     *
     * @return ApplicationPath|null
     */
    public function getActiveApplicationPath()
    {
        // get application path active at the time when the application path was locked
        return $this->getIrhpPermitType()->getActiveApplicationPath($this->getApplicationPathLockedOn());
    }
}
