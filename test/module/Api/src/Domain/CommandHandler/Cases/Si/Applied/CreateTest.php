<?php

/**
 * Create Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Cases\Si\Applied;

use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Si\Applied\Create as CreatePenalty;
use Dvsa\Olcs\Api\Domain\Repository\Cases as CasesRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Cases\Si\Applied\Create as Cmd;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
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
        $this->mockRepo('Cases', CasesRepo::class);
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
        $siId = 333;
        $penaltyId = 111;
        $caseId = 24;
        $startDate = '2015-12-25';
        $endDate = '2015-12-26';
        $imposed = 'Y';
        $imposedReason = 'reason';

        $command = Cmd::Create(
            [
                'case' => $caseId,
                'siPenaltyType' => $siPenaltyType,
                'imposed' => $imposed,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'imposedReason' => $imposedReason
            ]
        );

        $seriousInfringement = m::mock(SiEntity::class)->makePartial();
        $seriousInfringement->setId($siId);
        $seriousInfringements = new ArrayCollection([$seriousInfringement]);

        $caseEntity = m::mock(CasesEntity::class)->makePartial();
        $caseEntity->shouldReceive('getSeriousInfringements')->once()->andReturn($seriousInfringements);
        $caseEntity->shouldReceive('getId')->once()->andReturn($caseId);

        $this->repoMap['Cases']->shouldReceive('fetchById')->with($caseId)->once()->andReturn($caseEntity);

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
                'case' => $caseId,
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
