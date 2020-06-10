<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Cases\Si\Applied;

use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Si\Applied\Update as UpdatePenalty;
use Dvsa\Olcs\Api\Domain\Repository\SiPenalty as SiPenaltyRepo;
use Dvsa\Olcs\Api\Entity\Si\SiPenalty as SiPenaltyEntity;
use Dvsa\Olcs\Api\Entity\Si\SiPenaltyType as SiPenaltyTypeEntity;
use Dvsa\Olcs\Transfer\Command\Cases\Si\Applied\Update as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Update Test
 */
class UpdateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdatePenalty();
        $this->mockRepo('SiPenalty', SiPenaltyRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->references = [
            SiPenaltyTypeEntity::class => [
                999 => m::mock(SiPenaltyTypeEntity::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $siPenaltyType = 999;
        $penaltyId = 111;
        $startDate = '2015-12-25';
        $endDate = '2015-12-26';
        $imposed = 'Y';
        $imposedReason = 'reason';

        $command = Cmd::create(
            [
                'id' => $penaltyId,
                'siPenaltyType' => $siPenaltyType,
                'imposed' => $imposed,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'imposedReason' => $imposedReason
            ]
        );

        $penaltyEntity = m::mock(SiPenaltyEntity::class)->makePartial();
        $penaltyEntity->shouldReceive('getId')->once()->andReturn($penaltyId);
        $penaltyEntity->shouldReceive('getSeriousInfringement->getCase->isOpenErruCase')->once()->andReturn(true);

        $this->repoMap['SiPenalty']->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($penaltyEntity);
        $this->repoMap['SiPenalty']->shouldReceive('save')
            ->with(m::type(SiPenaltyEntity::class))
            ->once();

        $expected = [
            'id' => [
                'penalty' => $penaltyId
            ],
            'messages' => [
                'Applied penalty updated'
            ]
        ];

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf('Dvsa\Olcs\Api\Domain\Command\Result', $result);
        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandThrowsExceptionWhenCaseClosed()
    {
        $this->expectException(\Dvsa\Olcs\Api\Domain\Exception\ValidationException::class);

        $penaltyId = 111;

        $command = Cmd::create(
            [
                'id' => $penaltyId,
            ]
        );

        $penaltyEntity = m::mock(SiPenaltyEntity::class)->makePartial();
        $penaltyEntity->shouldReceive('getSeriousInfringement->getCase->isOpenErruCase')->once()->andReturn(false);

        $this->repoMap['SiPenalty']->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($penaltyEntity);

        $this->sut->handleCommand($command);
    }
}
