<?php

namespace Dvsa\OlcsTest\Api\Entity\Pi;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Pi\Pi as Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\Pi\PresidingTc as PresidingTcEntity;
use Dvsa\Olcs\Api\Entity\Pi\PiHearing as PiHearingEntity;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\System\Sla as SlaEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Mockery as m;

/**
 * Pi Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class PiEntityTest extends EntityTester
{
    public function setUp()
    {
        /** @var \Dvsa\Olcs\Api\Entity\Pi\Pi entity */
        $this->entity = $this->instantiate($this->entityClass);
    }

    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    /**
     * test create
     */
    public function testCreate()
    {
        $caseEntity = m::mock(CasesEntity::class);
        $agreedByTc = m::mock(PresidingTcEntity::class);
        $agreedByTcRole = m::mock(RefData::class);
        $piTypes = new ArrayCollection();
        $reasons = new ArrayCollection();
        $agreedDate = m::mock(\DateTime::class);
        $piStatus = m::mock(RefData::class);
        $comment = 'comment';

        $pi = new Entity(
            $caseEntity,
            $agreedByTc,
            $agreedByTcRole,
            $piTypes,
            $reasons,
            $agreedDate,
            $piStatus,
            $comment
        );

        $this->assertEquals($caseEntity, $pi->getCase());
        $this->assertEquals($agreedByTc, $pi->getAgreedByTc());
        $this->assertEquals($agreedByTcRole, $pi->getAgreedByTcRole());
        $this->assertEquals($piTypes, $pi->getPiTypes());
        $this->assertEquals($reasons, $pi->getReasons());
        $this->assertEquals($agreedDate, $pi->getAgreedDate());
        $this->assertEquals($piStatus, $pi->getPiStatus());
        $this->assertEquals($comment, $pi->getComment());
    }

    /**
     * test agreed and legislation
     */
    public function testAgreedAndLegislation()
    {
        $agreedByTc = m::mock(PresidingTcEntity::class);
        $agreedByTcRole = m::mock(RefData::class);
        $piTypes = new ArrayCollection();
        $reasons = new ArrayCollection();
        $agreedDate = m::mock(\DateTime::class);
        $comment = 'comment';

        $this->entity->updateAgreedAndLegislation(
            $agreedByTc,
            $agreedByTcRole,
            $piTypes,
            $reasons,
            $agreedDate,
            $comment
        );

        $this->assertEquals($agreedByTc, $this->entity->getAgreedByTc());
        $this->assertEquals($agreedByTcRole, $this->entity->getAgreedByTcRole());
        $this->assertEquals($piTypes, $this->entity->getPiTypes());
        $this->assertEquals($reasons, $this->entity->getReasons());
        $this->assertEquals($agreedDate, $this->entity->getAgreedDate());
        $this->assertEquals($comment, $this->entity->getComment());
    }

    /**
     * @dataProvider dateProvider
     *
     * @param string $inputDate
     * @param \DateTime|null $entityDate
     */
    public function testUpdatePiWithDecision($inputDate, $entityDate)
    {
        $decidedByTc = m::mock(PresidingTcEntity::class);
        $decidedByTcRole = m::mock(RefData::class);
        $decisions = new ArrayCollection();
        $licenceRevokedAtPi = 'Y';
        $licenceSuspendedAtPi = 'N';
        $licenceCurtailedAtPi = 'Y';
        $witnesses = 5;
        $decisionDate = $inputDate;
        $notificationDate = $inputDate;
        $decisionNotes = 'decision notes';

        $this->entity->updatePiWithDecision(
            $decidedByTc,
            $decidedByTcRole,
            $decisions,
            $licenceRevokedAtPi,
            $licenceSuspendedAtPi,
            $licenceCurtailedAtPi,
            $witnesses,
            $decisionDate,
            $notificationDate,
            $decisionNotes
        );

        $this->assertEquals($decidedByTc, $this->entity->getDecidedByTc());
        $this->assertEquals($decidedByTcRole, $this->entity->getDecidedByTcRole());
        $this->assertEquals($decisions, $this->entity->getDecisions());
        $this->assertEquals($licenceRevokedAtPi, $this->entity->getLicenceRevokedAtPi());
        $this->assertEquals($licenceSuspendedAtPi, $this->entity->getLicenceSuspendedAtPi());
        $this->assertEquals($licenceCurtailedAtPi, $this->entity->getLicenceCurtailedAtPi());
        $this->assertEquals($witnesses, $this->entity->getWitnesses());
        $this->assertEquals($entityDate, $this->entity->getDecisionDate());
        $this->assertEquals($entityDate, $this->entity->getNotificationDate());
        $this->assertEquals($decisionNotes, $this->entity->getDecisionNotes());
    }

    /**
     * @dataProvider dateProvider
     *
     * @param string $inputDate
     * @param \DateTime|null $entityDate
     */
    public function testUpdateWrittenOutcomeNone($inputDate, $entityDate)
    {
        $writtenOutcome = m::mock(RefData::class);
        $callUpLetterDate = $inputDate;
        $briefToTcDate = $inputDate;

        $this->entity->updateWrittenOutcomeNone($writtenOutcome, $callUpLetterDate, $briefToTcDate);

        $this->assertEquals($writtenOutcome, $this->entity->getWrittenOutcome());
        $this->assertEquals($entityDate, $this->entity->getCallUpLetterDate());
        $this->assertEquals($entityDate, $this->entity->getBriefToTcDate());
        $this->assertEquals(null, $this->entity->getTcWrittenDecisionDate());
        $this->assertEquals(null, $this->entity->getDecisionLetterSentDate());
        $this->assertEquals(null, $this->entity->getTcWrittenReasonDate());
        $this->assertEquals(null, $this->entity->getWrittenReasonLetterDate());
    }

    /**
     * @dataProvider dateProvider
     *
     * @param string $inputDate
     * @param \DateTime|null $entityDate
     */
    public function testUpdateWrittenOutcomeDecision($inputDate, $entityDate)
    {
        $writtenOutcome = m::mock(RefData::class);
        $callUpLetterDate = $inputDate;
        $briefToTcDate = $inputDate;
        $tcWrittenDecisionDate = $inputDate;
        $decisionLetterSentDate = $inputDate;

        $this->entity->updateWrittenOutcomeDecision(
            $writtenOutcome,
            $callUpLetterDate,
            $briefToTcDate,
            $tcWrittenDecisionDate,
            $decisionLetterSentDate
        );

        $this->assertEquals($writtenOutcome, $this->entity->getWrittenOutcome());
        $this->assertEquals($entityDate, $this->entity->getCallUpLetterDate());
        $this->assertEquals($entityDate, $this->entity->getBriefToTcDate());
        $this->assertEquals($entityDate, $this->entity->getTcWrittenDecisionDate());
        $this->assertEquals($entityDate, $this->entity->getDecisionLetterSentDate());
        $this->assertEquals(null, $this->entity->getTcWrittenReasonDate());
        $this->assertEquals(null, $this->entity->getWrittenReasonLetterDate());
    }

    /**
     * @dataProvider dateProvider
     *
     * @param string $inputDate
     * @param \DateTime|null $entityDate
     */
    public function testUpdateWrittenOutcomeReason($inputDate, $entityDate)
    {
        $writtenOutcome = m::mock(RefData::class);
        $callUpLetterDate = $inputDate;
        $briefToTcDate = $inputDate;
        $tcWrittenReasonDate = $inputDate;
        $writtenReasonLetterDate = $inputDate;

        $this->entity->updateWrittenOutcomeReason(
            $writtenOutcome,
            $callUpLetterDate,
            $briefToTcDate,
            $tcWrittenReasonDate,
            $writtenReasonLetterDate
        );

        $this->assertEquals($writtenOutcome, $this->entity->getWrittenOutcome());
        $this->assertEquals($entityDate, $this->entity->getCallUpLetterDate());
        $this->assertEquals($entityDate, $this->entity->getBriefToTcDate());
        $this->assertEquals(null, $this->entity->getTcWrittenDecisionDate());
        $this->assertEquals(null, $this->entity->getDecisionLetterSentDate());
        $this->assertEquals($entityDate, $this->entity->getTcWrittenReasonDate());
        $this->assertEquals($entityDate, $this->entity->getWrittenReasonLetterDate());
    }

    /**
     * @dataProvider canCloseWithHearingProvider
     *
     * @param $cancelledDate
     * @param $closedDate
     * @param $returnValue
     */
    public function testCanCloseWithHearing($cancelledDate, $closedDate, $returnValue)
    {
        $piHearing = m::mock(PiHearingEntity::class);
        $piHearing->shouldReceive('getCancelledDate')->andReturn($cancelledDate);
        $piHearings = new ArrayCollection([$piHearing]);

        $writtenOutcome = m::mock(RefData::class);
        $writtenOutcome->shouldReceive('getId')->andReturn(null);

        $this->entity->setClosedDate($closedDate);
        $this->entity->setPiHearings($piHearings);
        $this->entity->setWrittenOutcome($writtenOutcome);

        $this->assertEquals($returnValue, $this->entity->canClose());
    }

    public function canCloseWithHearingProvider()
    {
        $date = '2015-12-25';

        return [
            [$date, null, true],
            [$date, $date, false],
            [null, $date, false]
        ];
    }


    public function testCanCloseNoHearingNoOutcome()
    {
        $writtenOutcome = m::mock(RefData::class);
        $writtenOutcome->shouldReceive('getId')->andReturn(null);
        $this->entity->setPiHearings(new ArrayCollection());
        $this->entity->setWrittenOutcome($writtenOutcome);

        $this->assertEquals(false, $this->entity->canClose());
    }

    /**
     * @dataProvider canCloseWithOutcomeProvider
     *
     * @param $writtenOutcomeId
     * @param $tcWrittenReasonDate
     * @param $writtenReasonLetterDate
     * @param $tcWrittenDecisionDate
     * @param $decisionLetterSentDate
     * @param $closedDate
     * @param $returnValue
     */
    public function testCanCloseWithOutcome(
        $writtenOutcomeId,
        $tcWrittenReasonDate,
        $writtenReasonLetterDate,
        $tcWrittenDecisionDate,
        $decisionLetterSentDate,
        $closedDate,
        $returnValue
    ) {
        $writtenOutcome = m::mock(RefData::class);
        $writtenOutcome->shouldReceive('getId')->andReturn($writtenOutcomeId);
        $this->entity->setClosedDate($closedDate);
        $this->entity->setPiHearings(new ArrayCollection());
        $this->entity->setWrittenOutcome($writtenOutcome);
        $this->entity->setTcWrittenReasonDate($tcWrittenReasonDate);
        $this->entity->setWrittenReasonLetterDate($writtenReasonLetterDate);
        $this->entity->setTcWrittenDecisionDate($tcWrittenDecisionDate);
        $this->entity->setDecisionLetterSentDate($decisionLetterSentDate);

        $this->assertEquals($returnValue, $this->entity->canClose());
    }

    public function canCloseWithOutcomeProvider()
    {
        $date = '2015-12-25';

        return [
            [SlaEntity::WRITTEN_OUTCOME_NONE, null, null, null, null, null, true],
            [SlaEntity::WRITTEN_OUTCOME_NONE, null, null, null, null, $date, false],
            [SlaEntity::WRITTEN_OUTCOME_DECISION, null, null, $date, $date, $date, false],
            [SlaEntity::WRITTEN_OUTCOME_DECISION, null, null, $date, $date, null, true],
            [SlaEntity::WRITTEN_OUTCOME_DECISION, null, null, $date, null, null, false],
            [SlaEntity::WRITTEN_OUTCOME_DECISION, null, null, null, $date, $date, false],
            [SlaEntity::WRITTEN_OUTCOME_REASON, $date, $date, null, null, $date, false],
            [SlaEntity::WRITTEN_OUTCOME_REASON, $date, $date, null, null, null, true],
            [SlaEntity::WRITTEN_OUTCOME_REASON, $date, null, null, null, null, false],
            [SlaEntity::WRITTEN_OUTCOME_REASON, null, $date, null, null, $date, false]
        ];
    }

    /**
     * @dataProvider testGetHearingDateProvider
     *
     * @param string $hearingDate
     * @param string $isAdjourned
     * @param string $isCancelled
     * @param string|null $returnValue
     */
    public function testGetHearingDate($hearingDate, $isAdjourned, $isCancelled, $returnValue)
    {
        $piHearing = m::mock(PiHearingEntity::class);
        $piHearing->shouldReceive('getHearingDate')->andReturn($hearingDate);
        $piHearing->shouldReceive('getIsAdjourned')->andReturn($isAdjourned);
        $piHearing->shouldReceive('getIsCancelled')->andReturn($isCancelled);

        $this->entity->setPiHearings(new ArrayCollection([$piHearing]));

        $this->assertEquals($returnValue, $this->entity->getHearingDate());
    }

    /**
     * @return array
     */
    public function testGetHearingDateProvider()
    {
        $date = '2015-12-25';

        return [
            [$date, 'Y', 'Y', null],
            [$date, 'N', 'Y', null],
            [$date, 'Y', 'N', null],
            [$date, 'N', 'N', $date],
        ];
    }

    /**
     * Tests getCalculatedBundleValues
     */
    public function testGetCalculatedBundleValues()
    {
        $isTm = true;

        $cases = m::mock(CasesEntity::class);
        $cases->shouldReceive('isTm')->andReturn($isTm);

        $this->entity->setCase($cases);
        $this->entity->setPiHearings(new ArrayCollection());

        $expected = [
            'isClosed' => false,
            'canReopen' => false,
            'hearingDate' => null,
            'isTm' => $isTm
        ];

        $this->assertEquals($expected, $this->entity->getCalculatedBundleValues());
    }

    /**
     * provider to check dates are processed properly
     *
     * @return array
     */
    public function dateProvider()
    {
        $date = '2015-12-25';

        return [
            ['invalid date', null],
            [$date, \DateTime::createFromFormat('Y-m-d', $date)->setTime(0, 0, 0)]
        ];
    }
}
