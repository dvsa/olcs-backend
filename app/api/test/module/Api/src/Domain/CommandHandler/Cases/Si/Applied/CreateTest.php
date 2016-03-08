<?php

/**
 * Create Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Cases\Si\Applied;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Si\Applied\Create as CreatePenalty;
use Dvsa\Olcs\Api\Domain\Repository\SeriousInfringement as SiRepo;
use Dvsa\Olcs\Api\Domain\Repository\SiPenalty as SiPenaltyRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Cases\Si\Applied\Create as Cmd;
use Dvsa\Olcs\Api\Entity\Si\SeriousInfringement as SiEntity;
use Dvsa\Olcs\Api\Entity\Si\SiPenalty as SiPenaltyEntity;
use Dvsa\Olcs\Api\Entity\Si\SiPenaltyType as SiPenaltyTypeEntity;

/**
 * Create Test
 */
class CreateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreatePenalty();
        $this->mockRepo('SeriousInfringement', SiRepo::class);
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
        $siId = 333;
        $penaltyId = 111;
        $startDate = '2015-12-25';
        $endDate = '2015-12-26';
        $imposed = 'Y';
        $imposedReason = 'reason';

        $command = Cmd::Create(
            [
                'si' => $siId,
                'siPenaltyType' => $siPenaltyType,
                'imposed' => $imposed,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'imposedReason' => $imposedReason
            ]
        );

        $siEntity = m::mock(SiEntity::class)->makePartial();
        $siEntity->setId($siId);

        $this->repoMap['SeriousInfringement']->shouldReceive('fetchById')->with($siId)->once()->andReturn($siEntity);

        $this->repoMap['SiPenalty']->shouldReceive('save')
            ->with(m::type(SiPenaltyEntity::class))
            ->once()
            ->andReturnUsing(
                function (SiPenaltyEntity $siPenalty) {
                    $siPenalty->setId(111);
                }
            );

        $expected = [
            'id' => [
                'si' => $siId,
                'penalty' => $penaltyId
            ],
            'messages' => [
                'Applied penalty created'
            ]
        ];

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf('Dvsa\Olcs\Api\Domain\Command\Result', $result);
        $this->assertEquals($expected, $result->toArray());
    }
}
