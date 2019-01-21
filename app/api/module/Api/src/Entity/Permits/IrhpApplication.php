<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\OrganisationProviderInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\SectionableInterface;
use Dvsa\Olcs\Api\Entity\Traits\SectionTrait;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;

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
    SectionableInterface
{
    use SectionTrait;

    const ERR_CANT_CHECK_ANSWERS = 'Unable to check answers: the sections of the application have not been completed.';

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
            'sectionCompletion' => $this->getSectionCompletion(),
            'hasCheckedAnswers' => $this->hasCheckedAnswers(),
            'hasMadeDeclaration' => $this->hasMadeDeclaration(),
            'isNotYetSubmitted' => $this->isNotYetSubmitted(),
            'isReadyForNoOfPermits' => $this->isReadyForNoOfPermits(),
            'canCheckAnswers' => $this->canCheckAnswers(),
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
     * @return OrganisationEntity
     */
    public function getRelatedOrganisation()
    {
        return $this->getLicence()->getOrganisation();
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
    public function isActive()
    {
        return $this->isNotYetSubmitted() || $this->isUnderConsideration() || $this->isAwaitingFee()
            || $this->isFeePaid();
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
     * Update checkedAnswers to true
     *
     */
    public function updateCheckAnswers()
    {
        if (!$this->canCheckAnswers()) {
            throw new ForbiddenException(self::ERR_CANT_CHECK_ANSWERS);
        }
        return $this->checkedAnswers = true;
    }

    /**
     * Whether checkedAnswers can be be updated
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
        return ($this->getLatestOutstandingIrhpApplicationFee() !== null);
    }

    /**
     * Get Latest Outstanding Irhp Application Fee
     *
     * @return FeeEntity|null
     */
    public function getLatestOutstandingIrhpApplicationFee()
    {
        $feeTypeIds = [FeeTypeEntity::FEE_TYPE_IRHP_APP, FeeTypeEntity::FEE_TYPE_IRHP_ISSUE];
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
}
