<?php

/**
 * Update Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Cases\Si\Applied;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Si\Applied\Update as UpdatePenalty;
use Dvsa\Olcs\Api\Domain\Repository\Cases as CasesRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Cases\Si\Applied\Update as Cmd;
use Dvsa\Olcs\Api\Entity\Si\SiPenalty as SiPenaltyEntity;
use Dvsa\Olcs\Api\Entity\Si\SiPenaltyType as SiPenaltyTypeEntity;

/**
 * Update Test
 */
class UpdateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdatePenalty();
        $this->mockRepo('SiPenalty', CasesRepo::class);

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

        $command = Cmd::Create(
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
}
