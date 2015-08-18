<?php

namespace Dvsa\Olcs\Api\Entity\Pi;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Dvsa\Olcs\Api\Entity\Pi\PresidingTc as PresidingTcEntity;
use Dvsa\Olcs\Api\Entity\Pi\PiHearing as PiHearingEntity;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\System\Sla as SlaEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * Pi Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="pi",
 *    indexes={
 *        @ORM\Index(name="ix_pi_case_id", columns={"case_id"}),
 *        @ORM\Index(name="ix_pi_pi_status", columns={"pi_status"}),
 *        @ORM\Index(name="ix_pi_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_pi_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_pi_assigned_to", columns={"assigned_to"}),
 *        @ORM\Index(name="ix_pi_agreed_by_tc_id", columns={"agreed_by_tc_id"}),
 *        @ORM\Index(name="ix_pi_decided_by_tc_id", columns={"decided_by_tc_id"}),
 *        @ORM\Index(name="ix_pi_agreed_by_tc_role", columns={"agreed_by_tc_role"}),
 *        @ORM\Index(name="ix_pi_decided_by_tc_role", columns={"decided_by_tc_role"}),
 *        @ORM\Index(name="ix_pi_written_outcome", columns={"written_outcome"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_pi_olbs_key_olbs_type", columns={"olbs_key","olbs_type"})
 *    }
 * )
 */
class Pi extends AbstractPi
{
    const STATUS_REGISTERED = 'pi_s_reg';

    /**
     * @param CasesEntity $case
     * @param PresidingTc $agreedByTc
     * @param RefData $agreedByTcRole
     * @param ArrayCollection $piTypes
     * @param ArrayCollection $reasons
     * @param \DateTime $agreedDate
     * @param RefData $piStatus
     * @param String $comment
     */
    public function __construct(
        CasesEntity $case,
        PresidingTcEntity $agreedByTc,
        RefData $agreedByTcRole,
        ArrayCollection $piTypes,
        ArrayCollection $reasons,
        \DateTime $agreedDate,
        RefData $piStatus,
        $comment
    ) {
        parent::__construct();

        $this->create($case, $agreedByTc, $agreedByTcRole, $piTypes, $reasons, $agreedDate, $piStatus, $comment);
    }

    /**
     * @param CasesEntity $case
     * @param PresidingTc $agreedByTc
     * @param RefData $agreedByTcRole
     * @param ArrayCollection $piTypes
     * @param ArrayCollection $reasons
     * @param \DateTime $agreedDate
     * @param RefData $piStatus
     * @param String $comment
     */
    private function create(
        CasesEntity $case,
        PresidingTcEntity $agreedByTc,
        RefData $agreedByTcRole,
        ArrayCollection $piTypes,
        ArrayCollection $reasons,
        \DateTime $agreedDate,
        RefData $piStatus,
        $comment
    ) {
        $this->case = $case;
        $this->agreedByTc = $agreedByTc;
        $this->agreedByTcRole = $agreedByTcRole;
        $this->piTypes = $piTypes;
        $this->reasons = $reasons;
        $this->agreedDate = $agreedDate;
        $this->piStatus = $piStatus;
        $this->comment = $comment;
    }

    /**
     * @param PresidingTc $agreedByTc
     * @param RefData $agreedByTcRole
     * @param ArrayCollection $piTypes
     * @param ArrayCollection $reasons
     * @param \DateTime $agreedDate
     * @param String $comment
     */
    public function updateAgreedAndLegislation(
        PresidingTcEntity $agreedByTc,
        RefData $agreedByTcRole,
        ArrayCollection $piTypes,
        ArrayCollection $reasons,
        \DateTime $agreedDate,
        $comment
    ) {
        $this->agreedByTc = $agreedByTc;
        $this->agreedByTcRole = $agreedByTcRole;
        $this->piTypes = $piTypes;
        $this->reasons = $reasons;
        $this->agreedDate = $agreedDate;
        $this->comment = $comment;
    }

    /**
     * @param PresidingTcEntity $decidedByTc
     * @param RefData $decidedByTcRole
     * @param ArrayCollection $decisions
     * @param $licenceRevokedAtPi
     * @param $licenceSuspendedAtPi
     * @param $licenceCurtailedAtPi
     * @param $witnesses
     * @param $decisionDate
     * @param $notificationDate
     * @param $decisionNotes
     */
    public function updatePiWithDecision(
        $decidedByTc,
        RefData $decidedByTcRole,
        ArrayCollection $decisions,
        $licenceRevokedAtPi,
        $licenceSuspendedAtPi,
        $licenceCurtailedAtPi,
        $witnesses,
        $decisionDate,
        $notificationDate,
        $decisionNotes
    ) {
        $this->setDecidedByTc($decidedByTc);
        $this->decidedByTcRole = $decidedByTcRole;
        $this->decisions = $decisions;
        $this->licenceRevokedAtPi = $licenceRevokedAtPi;
        $this->licenceSuspendedAtPi = $licenceSuspendedAtPi;
        $this->licenceCurtailedAtPi = $licenceCurtailedAtPi;
        $this->witnesses = $witnesses;
        $this->decisionNotes = $decisionNotes;
        $this->decisionDate = $decisionDate;
        $this->notificationDate = $notificationDate;

        $decisionDateTime = \DateTime::createFromFormat('Y-m-d', $decisionDate);
        $notificationDateTime = \DateTime::createFromFormat('Y-m-d', $notificationDate);

        if (!$decisionDateTime instanceof \DateTime) {
            $decisionDateTime = null;
        }

        if (!$notificationDateTime instanceof \DateTime) {
            $notificationDateTime = null;
        }
    }

    /**
     * Can the Pi be closed?
     *
     * @return bool
     */
    protected function canClose()
    {
        if ($this->piHearings->count()) {
            if ($this->piHearings->first()->getCancelledDate() !== null) {
                return !$this->isClosed();
            }
        }

        if ($this->writtenOutcome !== null) {
            $writtenOutcomeId = $this->writtenOutcome->getId();

            switch($writtenOutcomeId) {
                case SlaEntity::WRITTEN_OUTCOME_NONE:
                    return !$this->isClosed();
                case SlaEntity::WRITTEN_OUTCOME_REASON:
                    if ($this->tcWrittenReasonDate === null || $this->writtenReasonLetterDate === null) {
                        return false;
                    }
                    return !$this->isClosed();
                case SlaEntity::WRITTEN_OUTCOME_DECISION:
                    if ($this->tcWrittenDecisionDate === null || $this->decisionLetterSentDate === null) {
                        return false;
                    }
                    return !$this->isClosed();
            }
        }

        return false;
    }

    /**
     * Is this a Transport Manager Pi?
     */
    public function isTm() {
        return $this->case->isTm();
    }

    /**
     * Is the Pi closed?
     *
     * return bool
     */
    public function isClosed()
    {
        return (bool) $this->closedDate != null;
    }

    /**
     * Can the Pi be reopened?
     *
     * @return bool
     */
    public function canReopen()
    {
        return $this->isClosed();
    }

    /**
     * Gets the upcoming hearing date
     */
    public function getHearingDate()
    {
        if ($this->piHearings->count()) {
            /** @var PiHearingEntity $hearing */
            $hearing = $this->piHearings->last();

            if ($hearing->getIsAdjourned() !== 'Y' && $hearing->getIsCancelled() !== 'Y') {
                return $hearing->getHearingDate();
            }
        }

        return null;
    }

    /**
     * Calculated values to be added to a bundle
     *
     * @return array
     */
    protected function getCalculatedBundleValues()
    {
        return [
            'isClosed' => $this->isClosed(),
            'canReopen' => $this->canReopen(),
            'hearingDate' => $this->getHearingDate(),
            'isTm' => $this->isTm()
        ];
    }
}
