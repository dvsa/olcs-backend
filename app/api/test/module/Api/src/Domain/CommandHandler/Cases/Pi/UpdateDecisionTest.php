<?php

/**
 * Update Decision Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Cases\Pi;

use Doctrine\ORM\Query;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Pi\UpdateDecision;
use Dvsa\Olcs\Api\Domain\Repository\Pi as PiRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Cases\Pi\UpdateDecision as Cmd;
use Dvsa\Olcs\Api\Entity\Pi\Pi as PiEntity;
use Dvsa\Olcs\Api\Entity\Pi\PresidingTc as PresidingTcEntity;
use Dvsa\Olcs\Api\Entity\Pi\Decision as PiDecisionEntity;
use Dvsa\Olcs\Api\Entity\Pi\PiHearing as PiHearingEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Publication\PiDecision as PublishDecisionCmd;

/**
 * Update Decision Test
 */
class UpdateDecisionTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateDecision();
        $this->mockRepo('Pi', PiRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            'tc_r_dhtru'
        ];

        $this->references = [
            PresidingTcEntity::class => [
                2 => m::mock(PresidingTcEntity::class)
            ],
            PiDecisionEntity::class => [
                65 => m::mock(PiDecisionEntity::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $licenceRevokedAtPi = 'Y';
        $licenceSuspendedAtPi = 'N';
        $licenceCurtailedAtPi = 'Y';
        $id = 11;
        $version = 22;
        $witnesses = 33;
        $decidedByTc = 44;
        $decidedByTcRole = 'tc_r_dhtru';
        $decisionDate = null;
        $notificationDate = null;
        $decisionNotes = 'decision notes';
        $publish = 'Y';
        $pubType = 'A&D';
        $trafficAreas = ['M'];
        $decisions = [66];
        $hearingId = 77;

        $command = Cmd::Create(
            [
                'id' => $id,
                'version' => $version,
                'decidedByTc' => $decidedByTc,
                'decidedByTcRole' => $decidedByTcRole,
                'decisionDate' => $decisionDate,
                'notificationDate' => $notificationDate,
                'witnesses' => $witnesses,
                'decisions' => $decisions,
                'licenceRevokedAtPi' => $licenceRevokedAtPi,
                'licenceCurtailedAtPi' => $licenceCurtailedAtPi,
                'licenceSuspendedAtPi' => $licenceSuspendedAtPi,
                'decisionNotes' => $decisionNotes,
                'publish' => $publish,
                'pubType' => $pubType,
                'trafficAreas' => $trafficAreas
            ]
        );

        $hearing1 = m::mock(PiHearingEntity::class);
        $lastHearing = m::mock(PiHearingEntity::class);
        $lastHearing->shouldReceive('getId')->andReturn($hearingId);

        $hearings = new ArrayCollection([$hearing1, $lastHearing]);

        /** @var PiEntity $pi */
        $pi = m::mock(PiEntity::class)->makePartial();
        $pi->shouldReceive('getPiHearings')->andReturn($hearings);

        $this->repoMap['Pi']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, $version)
            ->andReturn($pi)
            ->shouldReceive('save')
            ->with(m::type(PiEntity::class))
            ->once();

        $result1 = new Result();

        $expectedData = [
            'id' => $hearingId,
            'pubType' => [$pubType],
            'trafficAreas' => $trafficAreas,
            'text2' => $decisionNotes
        ];

        $this->expectedSideEffect(PublishDecisionCmd::class, $expectedData, $result1);

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ValidationException
     */
    public function testHandleCommandNoHearings()
    {
        $licenceRevokedAtPi = 'Y';
        $licenceSuspendedAtPi = 'N';
        $licenceCurtailedAtPi = 'Y';
        $id = 11;
        $version = 22;
        $witnesses = 33;
        $decidedByTc = 44;
        $decidedByTcRole = 'tc_r_dhtru';
        $decisionDate = null;
        $notificationDate = null;
        $decisionNotes = 'decision notes';
        $publish = 'Y';
        $pubType = 'A&D';
        $trafficAreas = ['M'];
        $decisions = [66];

        $command = Cmd::Create(
            [
                'id' => $id,
                'version' => $version,
                'decidedByTc' => $decidedByTc,
                'decidedByTcRole' => $decidedByTcRole,
                'decisionDate' => $decisionDate,
                'notificationDate' => $notificationDate,
                'witnesses' => $witnesses,
                'decisions' => $decisions,
                'licenceRevokedAtPi' => $licenceRevokedAtPi,
                'licenceCurtailedAtPi' => $licenceCurtailedAtPi,
                'licenceSuspendedAtPi' => $licenceSuspendedAtPi,
                'decisionNotes' => $decisionNotes,
                'publish' => $publish,
                'pubType' => $pubType,
                'trafficAreas' => $trafficAreas
            ]
        );

        /** @var PiEntity $pi */
        $pi = m::mock(PiEntity::class)->makePartial();
        $pi->shouldReceive('getPiHearings')->andReturn([]);

        $this->repoMap['Pi']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, $version)
            ->andReturn($pi)
            ->shouldReceive('save')
            ->with(m::type(PiEntity::class))
            ->once();

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);
    }
}
