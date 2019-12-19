<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\CancelableInterface;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtShortTermSuccessful;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtShortTermUnsuccessful;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtShortTermApsgPartSuccessful;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtShortTermAppSubmitted;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Entity\Generic\Answer;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationPath;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\Permits\Sectors;
use Dvsa\Olcs\Api\Entity\LicenceProviderInterface;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\OrganisationProviderInterface;
use Dvsa\Olcs\Api\Entity\SectionableInterface;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Task\Task;
use Dvsa\Olcs\Api\Entity\Traits\FetchPermitAppSubmissionTaskTrait;
use Dvsa\Olcs\Api\Entity\Traits\SectionTrait;
use Dvsa\Olcs\Api\Entity\Traits\PermitAppReviveFromUnsuccessfulTrait;
use Dvsa\Olcs\Api\Entity\Traits\PermitAppReviveFromWithdrawnTrait;
use Dvsa\Olcs\Api\Entity\Permits\Traits\ApplicationAcceptConsts;
use Dvsa\Olcs\Api\Entity\Permits\Traits\ApplicationAcceptScoringInterface;
use Dvsa\Olcs\Api\Entity\Permits\Traits\ApplicationAcceptScoringTrait;
use Dvsa\Olcs\Api\Entity\Permits\Traits\CandidatePermitCreationTrait;
use Dvsa\Olcs\Api\Entity\WithdrawableInterface;
use Dvsa\Olcs\Api\Service\Document\ContextProviderInterface;
use Dvsa\Olcs\Api\Service\Permits\Checkable\CheckableApplicationInterface;
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
    CancelableInterface,
    WithdrawableInterface,
    ContextProviderInterface,
    ApplicationAcceptScoringInterface,
    CheckableApplicationInterface
{
    use SectionTrait, CandidatePermitCreationTrait, ApplicationAcceptScoringTrait, FetchPermitAppSubmissionTaskTrait;

    use PermitAppReviveFromWithdrawnTrait {
        canBeRevivedFromWithdrawn as baseCanBeRevivedFromWithdrawn;
    }

    use PermitAppReviveFromUnsuccessfulTrait {
        canBeRevivedFromUnsuccessful as baseCanBeRevivedFromUnsuccessful;
    }

    const ERR_CANT_CANCEL = 'Unable to cancel this application';
    const ERR_CANT_CHECK_ANSWERS = 'Unable to check answers: the sections of the application have not been completed.';
    const ERR_CANT_MAKE_DECLARATION = 'Unable to make declaration: the sections of the application have not been completed.';
    const ERR_CANT_SUBMIT = 'This application cannot be submitted';
    const ERR_CANT_ISSUE = 'This application cannot be issued';
    const ERR_CANT_GRANT = 'Unable to grant this application';

    const ERR_ROADWORTHINESS_ONLY = 'This method is only for roadworthiness certificates';
    const ERR_ROADWORTHINESS_MOT_EXPIRY = 'The MOT has not yet expired on this record';

    const SECTIONS = [
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
            'canBeWithdrawn' => $this->canBeWithdrawn(),
            'canBeGranted' => $this->canBeGranted(),
            'canBeDeclined' => $this->canBeDeclined(),
            'canBeSubmitted' => $this->canBeSubmitted(),
            'canBeRevivedFromWithdrawn' => $this->canBeRevivedFromWithdrawn(),
            'canBeRevivedFromUnsuccessful' => $this->canBeRevivedFromUnsuccessful(),
            'hasOutstandingFees' => $this->hasOutstandingFees(),
            'outstandingFeeAmount' => $this->getOutstandingFeeAmount(),
            'sectionCompletion' => $this->getSectionCompletion(),
            'hasCheckedAnswers' => $this->hasCheckedAnswers(),
            'hasMadeDeclaration' => $this->hasMadeDeclaration(),
            'isNotYetSubmitted' => $this->isNotYetSubmitted(),
            'isSubmittedForConsideration' => $this->isSubmittedForConsideration(),
            'isValid' => $this->isValid(),
            'isFeePaid' => $this->isFeePaid(),
            'isIssueInProgress' => $this->isIssueInProgress(),
            'isAwaitingFee' => $this->isAwaitingFee(),
            'isUnderConsideration' => $this->isUnderConsideration(),
            'isReadyForNoOfPermits' => $this->isReadyForNoOfPermits(),
            'isCancelled' => $this->isCancelled(),
            'isWithdrawn' => $this->isWithdrawn(),
            'isDeclined' => $this->isDeclined(),
            'isBilateral' => $this->isBilateral(),
            'isMultilateral' => $this->isMultilateral(),
            'canCheckAnswers' => $this->canCheckAnswers(),
            'canMakeDeclaration' => $this->canMakeDeclaration(),
            'permitsRequired' => $this->getPermitsRequired(),
            'canUpdateCountries' => $this->canUpdateCountries(),
            'questionAnswerData' => $this->getQuestionAnswerData(),
            'businessProcess' => $this->getBusinessProcess(),
            'requiresPreAllocationCheck' => $this->requiresPreAllocationCheck(),
        ];
    }

    /**
     * Get question and answer data
     *
     * @param bool $isSnapshot whether this data is for a snapshot
     *
     * @return array
     */
    public function getQuestionAnswerData(bool $isSnapshot = false): array
    {
        // kept for backward compatibility only
        if ($this->isBilateral()) {
            return $this->getQuestionAnswerDataBilateral();
        } elseif ($this->isMultilateral()) {
            return $this->getQuestionAnswerDataMultilateral();
        }

        // licence
        $answer = $this->licence->getLicNo();
        $status = !empty($answer)
            ? SectionableInterface::SECTION_COMPLETION_COMPLETED
            : SectionableInterface::SECTION_COMPLETION_NOT_STARTED;
        $question = $isSnapshot ? 'permits.check-answers.page.question.licence' : 'section.name.application/licence';

        $data = [
            'custom-licence' => [
                'section' => 'licence',
                'slug' => 'custom-licence',
                'questionShort' => 'section.name.application/licence',
                'question' => $question,
                'questionType' => Question::QUESTION_TYPE_STRING,
                'answer' =>  $answer,
                'status' => $status,
            ]
        ];
        $previousQuestionStatus = $status;

        // the Q&A solution
        $activeApplicationPath = $this->getActiveApplicationPath();

        if (isset($activeApplicationPath)) {
            /**
             * list of defined steps
             *
             * @var ApplicationStep $applicationStep
             */
            foreach ($activeApplicationPath->getApplicationSteps() as $applicationStep) {
                $question = $applicationStep->getQuestion();
                $answer = null;
                $status = SectionableInterface::SECTION_COMPLETION_CANNOT_START;

                if ($previousQuestionStatus === SectionableInterface::SECTION_COMPLETION_COMPLETED) {
                    $answer = $this->getAnswer($applicationStep, $isSnapshot);

                    $status = isset($answer)
                        ? SectionableInterface::SECTION_COMPLETION_COMPLETED
                        : SectionableInterface::SECTION_COMPLETION_NOT_STARTED;
                }

                $activeQuestionText = $question->getActiveQuestionText($this->getApplicationPathLockedOn());

                $questionJson = json_decode($activeQuestionText->getQuestionKey(), true);
                $questionKey = $questionJson['translateableText']['key'];
                $slug = $question->getSlug();

                $data[$slug] = [
                    'section' => $slug,
                    'slug' => $slug,
                    'questionShort' => $activeQuestionText->getQuestionShortKey(),
                    'question' => $questionKey,
                    'questionType' => $activeQuestionText->getQuestion()->getQuestionType()->getId(),
                    'answer' => $answer,
                    'status' => $status,
                ];
                $previousQuestionStatus = $status;
            }
        }

        //we don't include check answers and declaration on html snapshots
        if (!$isSnapshot) {
            // checked answers
            $answer = null;
            $status = SectionableInterface::SECTION_COMPLETION_CANNOT_START;

            if ($previousQuestionStatus === SectionableInterface::SECTION_COMPLETION_COMPLETED) {
                $answer = $this->getCheckedAnswers();
                $status = !empty($answer)
                    ? SectionableInterface::SECTION_COMPLETION_COMPLETED
                    : SectionableInterface::SECTION_COMPLETION_NOT_STARTED;
            }

            $data['custom-check-answers'] = [
                'section' => 'checkedAnswers',
                'slug' => 'custom-check-answers',
                'questionShort' => 'section.name.application/check-answers',
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

            $data['custom-declaration'] = [
                'section' => 'declaration',
                'slug' => 'custom-declaration',
                'questionShort' => 'section.name.application/declaration',
                'question' => 'section.name.application/declaration',
                'answer' => $answer,
                'status' => $status,
            ];
        }

        return $data;
    }

    /**
     * Get an answer to the given application step
     *
     * @param bool $isSnapshot whether the answer should be returned in the context of a snapshot
     *
     * @return mixed|null
     */
    public function getAnswer(ApplicationStep $applicationStep, bool $isSnapshot = false)
    {
        $question = $applicationStep->getQuestion();

        if ($question->isCustom()) {
            $formControlType = $question->getFormControlType();
            switch ($formControlType) {
                case Question::FORM_CONTROL_ECMT_REMOVAL_NO_OF_PERMITS:
                    return $this->getEcmtRemovalNoOfPermitsAnswer();
                case Question::FORM_CONTROL_ECMT_SHORT_TERM_NO_OF_PERMITS:
                    return $this->getEcmtShortTermNoOfPermitsAnswer($isSnapshot);
                case Question::FORM_CONTROL_ECMT_SHORT_TERM_INTERNATIONAL_JOURNEYS:
                    return $this->getInternationalJourneysAnswer();
                case Question::FORM_CONTROL_ECMT_SHORT_TERM_RESTRICTED_COUNTRIES:
                    return $this->getEcmtShortTermRestrictedCountriesAnswer($question);
                case Question::FORM_CONTROL_ECMT_SHORT_TERM_SECTORS:
                    return $this->getEcmtShortTermSectorsAnswer($isSnapshot);
                case Question::FORM_CONTROL_ECMT_REMOVAL_PERMIT_START_DATE:
                case Question::FORM_CONTROL_ECMT_SHORT_TERM_ANNUAL_TRIPS_ABROAD:
                case Question::FORM_CONTROL_ECMT_SHORT_TERM_EARLIEST_PERMIT_DATE:
                case Question::FORM_CONTROL_CERT_ROADWORTHINESS_MOT_EXPIRY_DATE:
                case Question::FORM_CONTROL_COMMON_CERTIFICATES:
                    return $this->getStandardQaAnswer($question);
            }

            throw new RuntimeException(
                sprintf(
                    'Unable to retrieve answer status for form control type %s',
                    $formControlType
                )
            );
        }

        return $this->getStandardQaAnswer($question);
    }

    /**
     * Get the answer corresponding to a question for a non-custom question type
     *
     * @param Question $question
     *
     * @return mixed|null
     */
    private function getStandardQaAnswer(Question $question)
    {
        $activeQuestionText = $question->getActiveQuestionText($this->getApplicationPathLockedOn());

        if (!isset($activeQuestionText)) {
            return null;
        }

        /** @var Answer $answer answers are indexed by question_text_id */
        $answer = $this->getAnswers()->get($activeQuestionText->getId());

        if (!isset($answer)) {
            return null;
        }

        return $answer->getValue();
    }

    /**
     * Get the number of permits answer value for a custom element of type ecmt removal
     *
     * @return int|null
     */
    private function getEcmtRemovalNoOfPermitsAnswer()
    {
        return $this->getFirstIrhpPermitApplication()->getPermitsRequired();
    }

    /**
     * Get the number of permits answer values for a custom element of type ecmt short term
     *
     * @param bool $isSnapshot whether this answer is being produced in context of a snapshot
     *
     * @return array|null
     */
    private function getEcmtShortTermNoOfPermitsAnswer($isSnapshot = false)
    {
        $irhpPermitApplication = $this->getFirstIrhpPermitApplication();

        $requiredEuro5 = $irhpPermitApplication->getRequiredEuro5();
        $requiredEuro6 = $irhpPermitApplication->getRequiredEuro6();

        if (is_null($requiredEuro5) || is_null($requiredEuro6)) {
            return null;
        }

        $answer = [];

        //for snapshots we include a heading containing the validity year
        if ($isSnapshot) {
            $irhpPermitStock = $this->getFirstIrhpPermitApplication()
                ->getIrhpPermitWindow()
                ->getIrhpPermitStock();

            $year = $irhpPermitStock->getValidityYear();
            if ($year == 2019) {
                $answer[] = 'Permits for '  . $year;
            } else {
                $answer[] = $irhpPermitStock->getPeriodNameKey();
            }
        }

        if ($requiredEuro5) {
            $answer[] = $requiredEuro5 . ' permits for Euro 5 minimum emission standard';
        }

        if ($requiredEuro6) {
            $answer[] = $requiredEuro6 . ' permits for Euro 6 minimum emission standard';
        }

        // TODO: how should these values be returned to accommodate translation etc?
        return $answer;
    }

    /**
     * Get the international journeys answer value
     *
     * @return int|null
     */
    private function getInternationalJourneysAnswer()
    {
        if (!is_null($this->internationalJourneys)) {
            return $this->internationalJourneys->getId();
        }

        return null;
    }

    /**
     * Get the number of permits answer values for a custom element of type ecmt short term
     *
     * @return array|null
     */
    private function getEcmtShortTermRestrictedCountriesAnswer($question)
    {
        $answer = $this->getStandardQaAnswer($question);
        if (is_null($answer)) {
            return $answer;
        }

        if ($answer) {
            $countryNames = [];
            foreach ($this->countrys as $country) {
                $countryNames[] = $country->getCountryDesc();
            }

            return [
                'Yes',
                implode(', ', $countryNames)
            ];
        }

        return ['No'];
    }

    /**
     * Get the sectors answer value
     *
     * @param bool $isSnapshot
     *
     * @return int|null
     */
    private function getEcmtShortTermSectorsAnswer($isSnapshot)
    {
        if (!is_null($this->sectors)) {
            if ($isSnapshot) {
                return $this->sectors->getName();
            }

            return $this->sectors->getId();
        }

        return null;
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
                'questionType' => Question::QUESTION_TYPE_STRING,
            ],
            [
                'question' => 'permits.irhp.countries.transporting',
                'answer' =>  $countriesArray,
                'questionType' => Question::QUESTION_TYPE_STRING,
            ],
            [
                'question' => 'permits.snapshot.number.required',
                'answer' =>  $this->getPermitsRequired(),
                'questionType' => Question::QUESTION_TYPE_INTEGER,
            ],
        ];

        foreach ($numberOfPermits as $country => $years) {
            foreach ($years as $year => $permitsRequired) {
                $data[] = [
                    'question' => sprintf('%s for %d', $country, $year),
                    'answer' => $permitsRequired,
                    'questionType' => Question::QUESTION_TYPE_INTEGER,
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
                'questionType' => Question::QUESTION_TYPE_STRING,
            ],
            [
                'question' => 'permits.snapshot.number.required',
                'answer' =>  $this->getPermitsRequired(),
                'questionType' => Question::QUESTION_TYPE_INTEGER,
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
                'answer' => $permitsRequired,
                'questionType' => Question::QUESTION_TYPE_INTEGER,
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
     * @todo save overhead here by skipping these checks once we can easily identify which permit types have issue fees
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
            if ($fee->isOutstanding() && $fee->getFeeType()->isIrhpApplicationIssue()) {
                return true;
            }
        }

        return false;
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
    public function isSubmittedForConsideration()
    {
        return $this->getIrhpPermitType()->isEcmtShortTerm() && $this->isUnderConsideration();
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
    public function isCancelled()
    {
        return $this->status->getId() === IrhpInterface::STATUS_CANCELLED;
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
    public function isDeclined(): bool
    {
        return $this->isWithdrawn()
            && $this->withdrawReason->getId() === WithdrawableInterface::WITHDRAWN_REASON_DECLINED;
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
     * Whether the permit application can be withdrawn optional withdraw reason
     *
     * @param RefData $reason reason application is being withdrawn
     *
     * @return bool
     */
    public function canBeWithdrawn(?RefData $reason = null): bool
    {
        if ($reason instanceof RefData && $reason->getId() === WithdrawableInterface::WITHDRAWN_REASON_DECLINED) {
            return $this->canBeDeclined();
        }

        return $this->isUnderConsideration() || ($this->isAwaitingFee() && $this->issueFeeOverdue());
    }

    /**
     * Whether the permit application can be granted
     *
     * @return bool
     */
    public function canBeGranted(): bool
    {
        return
            $this->isUnderConsideration()
            && $this->licence->isValid()
            && (string)$this->getBusinessProcess() === RefData::BUSINESS_PROCESS_APGG;
    }

    /**
     * Whether the permit application can be declined
     *
     * @return bool
     */
    public function canBeDeclined(): bool
    {
        return $this->isAwaitingFee();
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

        //currently can't do this check for bilateral and multilateral
        if (!$this->isMultiStock() && !$this->licence->canMakeIrhpApplication($this->getAssociatedStock(), $this)) {
            return false;
        }

        $sections = $this->getSectionCompletion();

        return $sections['allCompleted'];
    }

    /**
     * Whether the application has any outstanding fees
     *
     * @return bool
     */
    public function hasOutstandingFees()
    {
        $fee = $this->getLatestOutstandingFeeByTypes(
            [
                FeeTypeEntity::FEE_TYPE_IRHP_APP,
                FeeTypeEntity::FEE_TYPE_IRHP_ISSUE,
                FeeTypeEntity::FEE_TYPE_IRFOGVPERMIT,
            ]
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
     * Get a list of outstanding irfo permit fees relating to this application
     *
     * @return array
     */
    public function getOutstandingIrfoPermitFees()
    {
        return $this->getOutstandingFeesByType(FeeTypeEntity::FEE_TYPE_IRFOGVPERMIT);
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
    public function getLatestIssueFee()
    {
        $criteria = Criteria::create()
            ->orderBy(['invoicedDate' => Criteria::DESC]);

        /** @var FeeEntity $fee */
        foreach ($this->getFees()->matching($criteria) as $fee) {
            if ($fee->getFeeType()->getFeeType()->getId() == FeeTypeEntity::FEE_TYPE_IRHP_ISSUE) {
                return $fee;
            }
        }

        return null;
    }

    /**
     * Return the latest outstanding issue fee, or none if no issue fee is present
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
    public function getOutstandingFees(): array
    {
        $feeTypeIds = [
            FeeTypeEntity::FEE_TYPE_IRHP_APP,
            FeeTypeEntity::FEE_TYPE_IRHP_ISSUE,
            FeeTypeEntity::FEE_TYPE_IRFOGVPERMIT,
        ];
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
        return $this->isNotYetSubmitted() || $this->isUnderConsideration();
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

        switch ($this->getIrhpPermitType()->getId()) {
            case IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM:
                return $this->proceedToUnderConsideration($submitStatus);
            case IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL:
            case IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL:
            case IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL:
                return $this->proceedToIssuing($submitStatus);
            case IrhpPermitType::IRHP_PERMIT_TYPE_ID_CERT_ROADWORTHINESS_VEHICLE:
            case IrhpPermitType::IRHP_PERMIT_TYPE_ID_CERT_ROADWORTHINESS_TRAILER:
                return $this->proceedToValid($submitStatus);
            default:
                throw new ForbiddenException(self::ERR_CANT_SUBMIT);
        }
    }

    /**
     * Grant Application -
     *
     * @param RefData $grantStatus
     * @throws ForbiddenException
     */
    public function grant(RefData $grantStatus)
    {
        if (!$this->canBeGranted()) {
            throw new ForbiddenException(self::ERR_CANT_GRANT);
        }

        $this->status = $grantStatus;
    }

    /**
     * Withdraw an application
     *
     * @param RefData $withdrawStatus
     * @param RefData $withdrawReason
     *
     * @throws ForbiddenException
     * @return void
     */
    public function withdraw(RefData $withdrawStatus, RefData $withdrawReason): void
    {
        if (!$this->canBeWithdrawn($withdrawReason)) {
            $error = ($withdrawReason->getId() === WithdrawableInterface::WITHDRAWN_REASON_DECLINED)
                ? WithdrawableInterface::ERR_CANT_DECLINE : WithdrawableInterface::ERR_CANT_WITHDRAW;

            throw new ValidationException([$error]);
        }

        $this->status = $withdrawStatus;
        $this->withdrawReason = $withdrawReason;
        $this->withdrawnDate = new \DateTime();
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
     * Applicable only to bilateral, multilateral and ecmt short term
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
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM
                => FeeTypeEntity::FEE_TYPE_ECMT_APP_PRODUCT_REF,
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL
                => FeeTypeEntity::FEE_TYPE_ECMT_REMOVAL_ISSUE_PRODUCT_REF,
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
     * Gets the issue fee product reference for this application
     * Applicable only to ecmt short term
     *
     * @return string
     *
     * @throws ForbiddenException if the permit type is unsupported
     */
    public function getIssueFeeProductReference()
    {
        $irhpPermitTypeId = $this->getIrhpPermitType()->getId();

        if ($irhpPermitTypeId != IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM) {
            throw new ForbiddenException(
                'No issue fee product reference available for permit type ' . $irhpPermitTypeId
            );
        }

        return FeeTypeEntity::FEE_TYPE_ECMT_SHORT_TERM_ISSUE_PRODUCT_REF;
    }

    /**
     * Gets the total fee per permit including application fee and issue fee
     * Applicable only to bilateral, multilateral, removals and ecmt short term
     *
     * @param FeeTypeEntity $applicationFeeType (optional)
     * @param FeeTypeEntity $issueFeeType (optional)
     *
     * @return int
     *
     * @throws ForbiddenException if the permit type is unsupported
     */
    public function getFeePerPermit(?FeeTypeEntity $applicationFeeType = null, ?FeeTypeEntity $issueFeeType = null)
    {
        $permittedPermitTypeIds = [
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM,
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL,
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL,
        ];

        $irhpPermitTypeId = $this->getIrhpPermitType()->getId();

        if (!in_array($irhpPermitTypeId, $permittedPermitTypeIds)) {
            throw new ForbiddenException(
                'Cannot get fee per permit for irhp permit type ' . $irhpPermitTypeId
            );
        }

        if ($irhpPermitTypeId == IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL) {
            return $issueFeeType->getFixedValue();
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
     * Proceeds the application from not yet submitted status to under consideration status
     *
     * @param RefData $uc_status
     *
     * @throws ForbiddenException
     */
    public function proceedToUnderConsideration(RefData $uc_status)
    {
        if ($this->hasOutstandingFees()) {
            throw new ForbiddenException(self::ERR_CANT_SUBMIT);
        }

        $this->status = $uc_status;
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
        if (!$this->isIssueInProgress() && !$this->isCertificateOfRoadworthiness()) {
            throw new ForbiddenException(
                sprintf(
                    'This application is not in the correct state to proceed to valid (status: %s, irhpPermitType: %d)',
                    $this->status->getId(),
                    $this->irhpPermitType->getId()
                )
            );
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
        return $this->getFirstIrhpPermitApplication()
            ->getIrhpPermitWindow()
            ->getIrhpPermitStock()
            ->getApplicationPathGroup()
            ->getActiveApplicationPath($this->getApplicationPathLockedOn());
    }

    /**
     * {@inheritdoc}
     */
    public function getContextValue()
    {
        return $this->id;
    }

    /**
     * Retrieves the first linked irhp permit application instance
     *
     * @return IrhpPermitApplication
     *
     * @throws RuntimeException
     */
    public function getFirstIrhpPermitApplication()
    {
        if ($this->irhpPermitApplications->count() == 0) {
            throw new RuntimeException(
                'IrhpApplication has zero linked IrhpPermitApplication instances'
            );
        }

        return $this->irhpPermitApplications->first();
    }

    /**
     * Is this a Certificate of Roadworthiness application?
     *
     * @return bool
     */
    public function isCertificateOfRoadworthiness(): bool
    {
        return $this->irhpPermitType->isCertificateOfRoadworthiness();
    }

    /**
     * Is this a Certificate of Roadworthiness vehicle application?
     *
     * @return bool
     */
    public function isCertificateOfRoadworthinessVehicle(): bool
    {
        return $this->irhpPermitType->isCertificateOfRoadworthinessVehicle();
    }

    /**
     * Is this a Certificate of Roadworthiness trailer application?
     *
     * @return bool
     */
    public function isCertificateOfRoadworthinessTrailer(): bool
    {
        return $this->irhpPermitType->isCertificateOfRoadworthinessTrailer();
    }

    /**
     * Is this application a multi stock application?
     *
     * @return bool
     */
    public function isMultiStock(): bool
    {
        return $this->irhpPermitType->isMultiStock();
    }

    /**
     * Get the associated stock for this application
     *
     * @return IrhpPermitStock
     * @throws RuntimeException
     */
    public function getAssociatedStock(): IrhpPermitStock
    {
        if ($this->isMultiStock()) {
            throw new RuntimeException('Multi stock permit types can\'t use this method');
        }

        return $this->getFirstIrhpPermitApplication()->getIrhpPermitWindow()->getIrhpPermitStock();
    }

    /**
     * Changes the status to expired
     *
     * @param RefData       $expireStatus
     * @param DateTime|null $expiryDate
     *
     * @throws ForbiddenException
     */
    public function expire(RefData $expireStatus, DateTime $expiryDate = null)
    {
        if (!$this->canBeExpired()) {
            throw new ForbiddenException('This application cannot be expired.');
        }

        if ($expiryDate === null) {
            $expiryDate = new DateTime();
        }

        $this->status = $expireStatus;
        $this->expiryDate = $expiryDate;
    }

    /**
     * Whether the application can be expired
     *
     * @return bool
     */
    public function canBeExpired()
    {
        if (!$this->isValid()) {
            // only valid application can be expired
            return false;
        }

        foreach ($this->getIrhpPermitApplications() as $irhpPermitApplication) {
            if ($irhpPermitApplication->hasValidPermits()) {
                // only application without any valid permits can be expired
                return false;
            }
        }

        return true;
    }

    /**
     * Whether can view candidate permits
     *
     * @return bool
     */
    public function canViewCandidatePermits(): bool
    {
        return $this->isAwaitingFee()
            && $this->getAllocationMode() == IrhpPermitStock::ALLOCATION_MODE_CANDIDATE_PERMITS;
    }

    /**
     * Update the international journeys answer value
     *
     * @param RefData $internationalJourneys
     */
    public function updateInternationalJourneys(RefData $internationalJourneys)
    {
        $this->internationalJourneys = $internationalJourneys;
    }

    /**
     * Clear the international journeys answer value
     */
    public function clearInternationalJourneys()
    {
        $this->internationalJourneys = null;
    }

    /**
     * Update the sectors answer value
     *
     * @param Sectors $sectors
     */
    public function updateSectors(Sectors $sectors)
    {
        $this->sectors = $sectors;
    }

    /**
     * Clear the sectors answer value
     */
    public function clearSectors()
    {
        $this->sectors = null;
    }

    /**
     * Get the business process
     *
     * @return RefData|null
     */
    public function getBusinessProcess()
    {
        // get the business process related to the application
        try {
            return $this->getFirstIrhpPermitApplication()
                ->getIrhpPermitWindow()
                ->getIrhpPermitStock()
                ->getBusinessProcess();
        } catch (RuntimeException $ex) {
            // do nothing if getFirstIrhpPermitApplication() throws an exception
        }
    }
       
    /**
     * Get the answer value corresponding to the specified question id
     *
     * @param int $id
     *
     * @return mixed|null
     */
    public function getAnswerValueByQuestionId($id)
    {
        $applicationSteps = $this->getActiveApplicationPath()->getApplicationSteps();

        /** @var ApplicationStep $applicationStep */
        foreach ($applicationSteps as $applicationStep) {
            if ($applicationStep->getQuestion()->getId() == $id) {
                return $this->getAnswer($applicationStep, false);
            }
        }

        return null;
    }

    /**
     * Whether the application has an association with the specified country id
     *
     * @param string $id
     *
     * @return bool
     */
    public function hasCountryId($id)
    {
        foreach ($this->countrys as $country) {
            if ($country->getId() == $id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Update the list of countries associated with this application
     *
     * @param ArrayCollection $countries
     */
    public function updateCountries(ArrayCollection $countries)
    {
        $this->countrys = $countries;
    }
   
    /**
     * Get the total number of permits required by this application
     *
     * @return int
     *
     * @throws RuntimeException
     */
    public function calculateTotalPermitsRequired()
    {
        if (!$this->getIrhpPermitType()->isEcmtShortTerm()) {
            throw new RuntimeException('calculateTotalPermitsRequired is only applicable to ECMT short term');
        }
    
        $irhpPermitApplication = $this->getFirstIrhpPermitApplication();
        $requiredEuro5 = $irhpPermitApplication->getRequiredEuro5();
        $requiredEuro6 = $irhpPermitApplication->getRequiredEuro6();

        if (is_null($requiredEuro5) || is_null($requiredEuro6)) {
            throw new RuntimeException('This IRHP Application has not had number of required permits set yet.');
        }

        return $requiredEuro5 + $requiredEuro6;
    }

    /**
     * Calculates the intensity_of_use value for permits requested by an irhpApplication
     *
     * @param string $emissionsCategoryId|null
     *
     * return float
     *
     * @throws RuntimeException
     */
    public function getPermitIntensityOfUse($emissionsCategoryId = null)
    {
        $firstIrhpPermitApplication = $this->getFirstIrhpPermitApplication();

        if ($emissionsCategoryId == RefData::EMISSIONS_CATEGORY_EURO5_REF) {
            $numberOfPermits = $firstIrhpPermitApplication->getRequiredEuro5();
        } elseif ($emissionsCategoryId == RefData::EMISSIONS_CATEGORY_EURO6_REF) {
            $numberOfPermits = $firstIrhpPermitApplication->getRequiredEuro6();
        } elseif (is_null($emissionsCategoryId)) {
            $numberOfPermits = $this->calculateTotalPermitsRequired();
        } else {
            throw new RuntimeException(
                'Unexpected emissionsCategoryId parameter for getPermitIntensityOfUse: ' . $emissionsCategoryId
            );
        }

        // TODO: once scoring is used by more than one Q&A type, we'll need a more general method than using the
        // question slug to derive the answer to the annual trips abroad question

        return $this->calculatePermitIntensityOfUse(
            $this->getAnswerValueByQuestionId(Question::QUESTION_ID_SHORT_TERM_ANNUAL_TRIPS_ABROAD),
            $numberOfPermits
        );
    }

    /**
     * Calculates the application_score value for permits requested by an irhpApplication
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
     * Return the entity name in camel case
     *
     * @return string
     */
    public function getCamelCaseEntityName()
    {
        return 'irhpApplication';
    }

    /**
     * Return an array of mappings between success levels and email commands
     *
     * @return array
     */
    public function getEmailCommandLookup()
    {
        if (!$this->irhpPermitType->isEcmtShortTerm()) {
            throw new RuntimeException('getEmailCommandLookup is only applicable to ECMT short term');
        }

        return [
            ApplicationAcceptConsts::SUCCESS_LEVEL_NONE => SendEcmtShortTermUnsuccessful::class,
            ApplicationAcceptConsts::SUCCESS_LEVEL_PARTIAL => SendEcmtShortTermApsgPartSuccessful::class,
            ApplicationAcceptConsts::SUCCESS_LEVEL_FULL => SendEcmtShortTermSuccessful::class
        ];
    }

    /**
     * Return the number of trips above which the intensity of use should be classed as high intensity
     *
     * @return int
     */
    public function getIntensityOfUseWarningThreshold()
    {
        if (!$this->irhpPermitType->isEcmtShortTerm()) {
            throw new RuntimeException('getIntensityOfUseWarningThreshold is only applicable to ECMT short term');
        }

        $irhpPermitApplication = $this->getFirstIrhpPermitApplication();

        $highestRequiredPermits = max(
            $irhpPermitApplication->getRequiredEuro5(),
            $irhpPermitApplication->getRequiredEuro6()
        );

        return $highestRequiredPermits * 100;
    }

    /**
     * Return the command class name that represents the application submitted email for this application, or null if
     * no email command is applicable
     *
     * @return string|null
     */
    public function getAppSubmittedEmailCommand()
    {
        $isEcmtShortTerm = $this->irhpPermitType->isEcmtShortTerm();
        $isApsg = $this->getBusinessProcess()->getId() == RefData::BUSINESS_PROCESS_APSG;

        if ($isEcmtShortTerm && $isApsg) {
            return SendEcmtShortTermAppSubmitted::class;
        }

        return null;
    }

    /**
     * Get the permit allocation mode used by the stock associated with this application
     *
     * @return string
     */
    public function getAllocationMode()
    {
        return $this->getAssociatedStock()->getAllocationMode();
    }

    /**
     * Whether permits should be immediately allocated on submission for this application
     *
     * @return bool
     */
    public function shouldAllocatePermitsOnSubmission()
    {
        return $this->getBusinessProcess()->getId() == RefData::BUSINESS_PROCESS_APG;
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
     *
     * @throws RuntimeException
     */
    public function getSubmissionTaskDescription()
    {
        $mappings = [
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM
                => Task::TASK_DESCRIPTION_SHORT_TERM_ECMT_RECEIVED,
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL
                => Task::TASK_DESCRIPTION_ECMT_INTERNATIONAL_REMOVALS_RECEIVED,
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL
                => Task::TASK_DESCRIPTION_BILATERAL_RECEIVED,
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL
                => Task::TASK_DESCRIPTION_MULTILATERAL_RECEIVED,
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_CERT_ROADWORTHINESS_VEHICLE
                => Task::TASK_DESCRIPTION_CERT_ROADWORTHINESS_RECEIVED,
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_CERT_ROADWORTHINESS_TRAILER
                => Task::TASK_DESCRIPTION_CERT_ROADWORTHINESS_RECEIVED,
        ];

        $irhpPermitTypeId = $this->irhpPermitType->getId();

        if (!isset($mappings[$irhpPermitTypeId])) {
            throw new RuntimeException('No submission task description defined for permit type ' . $irhpPermitTypeId);
        }

        return $mappings[$irhpPermitTypeId];
    }

    /**
     * Get the MOT expiry date for a certificate of roadworthiness
     *
     * @return string|null
     */
    public function getMotExpiryDate(): ?string
    {
        if (!$this->isCertificateOfRoadworthiness()) {
            return null;
        }

        $questionId = Question::QUESTION_ID_ROADWORTHINESS_VEHICLE_MOT_EXPIRY;

        if ($this->isCertificateOfRoadworthinessTrailer()) {
            $questionId = Question::QUESTION_ID_ROADWORTHINESS_TRAILER_MOT_EXPIRY;
        }

        return $this->getAnswerValueByQuestionId($questionId);
    }

    /**
     * Return whether the MOT has expired
     *
     * @return bool
     */
    private function motHasExpired(): bool
    {
        $motExpiryDate = $this->getMotExpiryDate();

        //cover instances where expiry date not yet completed
        if ($motExpiryDate === null) {
            return false;
        }

        $currentDate = new \DateTime();

        //if MOT expiry date (returned by Q&A as a Y-m-d string) is not after the current date then it is still valid
        if ($motExpiryDate <= $currentDate->format('Y-m-d')) {
            return false;
        }

        return true;
    }

    /**
     * Expire certificate of roadworthiness
     *
     * @param RefData $expireStatus
     *
     * @throws \Exception
     *
     * @return void
     */
    public function expireCertificate(RefData $expireStatus): void
    {
        if (!$this->isCertificateOfRoadworthiness()) {
            throw new \Exception(self::ERR_ROADWORTHINESS_ONLY);
        }

        if (!$this->motHasExpired()) {
            throw new \Exception(self::ERR_ROADWORTHINESS_MOT_EXPIRY);
        }

        $this->expire(
            $expireStatus,
            DateTime::createFromFormat('Y-m-d', $this->getMotExpiryDate())
        );
    }

    /**
     * Get the status that this application should be set to upon submission
     *
     * @return string
     *
     * @throws RuntimeException
     */
    public function getSubmissionStatus()
    {
        $mappings = [
            RefData::BUSINESS_PROCESS_AG => IrhpInterface::STATUS_VALID,
            RefData::BUSINESS_PROCESS_APG => IrhpInterface::STATUS_ISSUING,
            RefData::BUSINESS_PROCESS_APGG => IrhpInterface::STATUS_UNDER_CONSIDERATION,
            RefData::BUSINESS_PROCESS_APSG => IrhpInterface::STATUS_UNDER_CONSIDERATION,
        ];

        $businessProcessId = $this->getBusinessProcess()->getId();

        if (!isset($mappings[$businessProcessId])) {
            throw new RuntimeException('No submission status defined for business process ' . $businessProcessId);
        }

        return $mappings[$businessProcessId];
    }

    /**
     * Get the candidate permit creation mode associated with this application
     *
     * @return string
     */
    public function getCandidatePermitCreationMode()
    {
        if ($this->isMultiStock()) {
            return IrhpPermitStock::CANDIDATE_MODE_NONE;
        }

        return $this->getAssociatedStock()->getCandidatePermitCreationMode();
    }
   
    /**
     * Whether this application needs to be manually checked by a case worker before permits are allocated
     *
     * @return bool
     */
    public function requiresPreAllocationCheck()
    {
        return $this->irhpPermitType->isEcmtShortTerm();
    }

    /**
     * Whether the permit application can be revived from a withdrawn state
     *
     * @return bool
     */
    public function canBeRevivedFromWithdrawn()
    {
        $businessProcess = $this->getBusinessProcess();

        if ($businessProcess === null) {
            return false;
        }

        $canBeRevivedFromWithdrawn = $this->baseCanBeRevivedFromWithdrawn();
        $isApsg = $businessProcess->getId() == RefData::BUSINESS_PROCESS_APSG;

        return $canBeRevivedFromWithdrawn && $isApsg;
    }

    /**
     * Whether the permit application can be revived from an unsuccessful state
     *
     * @return bool
     */
    public function canBeRevivedFromUnsuccessful()
    {
        $businessProcess = $this->getBusinessProcess();

        if ($businessProcess === null) {
            return false;
        }

        $canBeRevivedFromUnsuccessful = $this->baseCanBeRevivedFromUnsuccessful();
        $isApsg = $businessProcess->getId() == RefData::BUSINESS_PROCESS_APSG;

        return $canBeRevivedFromUnsuccessful && $isApsg;
    }
}
