<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Cases\Si\Applied;

use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Si\Applied\Delete as DeletePenalty;
use Dvsa\Olcs\Api\Domain\Repository\Cases as CasesRepo;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CaseEntity;
use Dvsa\Olcs\Api\Entity\Si\ErruRequest as ErruRequestEntity;
use Dvsa\Olcs\Api\Entity\Si\SiPenalty as SiPenaltyEntity;
use Dvsa\Olcs\Transfer\Command\Cases\Si\Applied\Delete as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Delete Test
 */
class DeleteTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new DeletePenalty();
        $this->mockRepo('SiPenalty', CasesRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $penaltyId = 111;

        $command = Cmd::Create(
            [
                'id' => $penaltyId,
            ]
        );

        $caseEntity = m::mock(CaseEntity::class)->makePartial();
        $caseEntity->shouldReceive('isClosed')->once()->andReturn(false);

        $penaltyEntity = m::mock(SiPenaltyEntity::class)->makePartial();
        $penaltyEntity->shouldReceive('getId')->once()->andReturn($penaltyId);
        $penaltyEntity->shouldReceive('getSeriousInfringement->getCase')->once()->andReturn($caseEntity);

        $this->repoMap['SiPenalty']->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($penaltyEntity);
        $this->repoMap['SiPenalty']->shouldReceive('delete')
            ->with(m::type(SiPenaltyEntity::class))
            ->once();

        $expected = [
            'id' => [
                'id' => $penaltyId
            ],
            'messages' => [
                'Applied penalty deleted'
            ]
        ];

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf('Dvsa\Olcs\Api\Domain\Command\Result', $result);
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * @expectedException Dvsa\Olcs\Api\Domain\Exception\ValidationException
     */
    public function testHandleCommandThrowsExceptionWhenCaseClosed()
    {
        $penaltyId = 111;

        $command = Cmd::Create(
            [
                'id' => $penaltyId,
            ]
        );

        $caseEntity = m::mock(CaseEntity::class)->makePartial();
        $caseEntity->shouldReceive('isClosed')->once()->andReturn(true);

        $penaltyEntity = m::mock(SiPenaltyEntity::class)->makePartial();
        $penaltyEntity->shouldReceive('getSeriousInfringement->getCase')->once()->andReturn($caseEntity);

        $this->repoMap['SiPenalty']->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($penaltyEntity);

        $this->sut->handleCommand($command);
    }

    /**
     * @expectedException Dvsa\Olcs\Api\Domain\Exception\ValidationException
     */
    public function testHandleCommandThrowsExceptionWhenErruRequestSent()
    {
        $penaltyId = 111;

        $command = Cmd::Create(
            [
                'id' => $penaltyId,
            ]
        );

        $erruRequestEntity = m::mock(ErruRequestEntity::class)->makePartial();
        $erruRequestEntity->setResponseSent('Y');

        $caseEntity = m::mock(CaseEntity::class)->makePartial();
        $caseEntity->shouldReceive('isClosed')->once()->andReturn(false);
        $caseEntity->shouldReceive('getErruRequest')->once()->andReturn($erruRequestEntity);

        $penaltyEntity = m::mock(SiPenaltyEntity::class)->makePartial();
        $penaltyEntity->shouldReceive('getSeriousInfringement->getCase')->once()->andReturn($caseEntity);

        $this->repoMap['SiPenalty']->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($penaltyEntity);

        $this->sut->handleCommand($command);
    }
}
