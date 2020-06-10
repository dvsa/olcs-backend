<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpPermitRange;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitRange\Update as UpdateHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitRange as PermitRangeRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as PermitStockRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\IrhpPermitRange\Update as UpdateCmd;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange as PermitRangeEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * Update IrhpPermitRange Test
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
class UpdateTest extends CommandHandlerTestCase
{
    use ProcessDateTrait;

    public function setUp()
    {
        $this->sut = new UpdateHandler();
        $this->mockRepo('IrhpPermitRange', PermitRangeRepo::class);
        $this->mockRepo('IrhpPermitStock', PermitStockRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            RefData::EMISSIONS_CATEGORY_EURO5_REF,
            RefData::EMISSIONS_CATEGORY_EURO6_REF,
            RefData::EMISSIONS_CATEGORY_NA_REF
        ];

        parent::initReferences();
    }

    /**
     * @dataProvider dpShortTermAnnualTypeCombinations
     */
    public function testHandleCommand($isEcmtShortTerm, $isEcmtAnnual)
    {
        $id = 1;
        $cmdData = [
            'irhpPermitStock' => '1',
            'emissionsCategory' => RefData::EMISSIONS_CATEGORY_EURO6_REF,
            'prefix' => 'UK',
            'fromNo' => 1,
            'toNo' => 100,
            'ssReserve' => 0,
            'lostReplacement' => 0,
            'countrys' => []
        ];

        $irhpPermitType = m::mock(IrhpPermitType::class);
        $irhpPermitType->shouldReceive('isEcmtShortTerm')->andReturn($isEcmtShortTerm);
        $irhpPermitType->shouldReceive('isEcmtAnnual')->andReturn($isEcmtAnnual);

        $stock = m::mock(IrhpPermitStock::class)->makePartial();
        $stock->shouldReceive('getIrhpPermitType')->andReturn($irhpPermitType);

        $command = UpdateCmd::create($cmdData);

        $entity = m::mock(PermitRangeEntity::class);

        $this->repoMap['IrhpPermitStock']
            ->shouldReceive('fetchById')
            ->with($command->getIrhpPermitStock())
            ->andReturn($stock);

        $entity->shouldReceive('update')
            ->with($stock, $this->refData[RefData::EMISSIONS_CATEGORY_EURO6_REF], 'UK', '1', '100', '0', '0', [])
            ->andReturn(m::mock(IrhpPermitRange::class));

        $entity->shouldReceive('getId')
            ->twice()
            ->andReturn($id);

        $this->repoMap['IrhpPermitRange']
            ->shouldReceive('fetchUsingId')
            ->once()
            ->with($command)
            ->andReturn($entity);

        $this->repoMap['IrhpPermitRange']->shouldReceive('findOverlappingRangesByType')
        ->andReturn([]);

        $this->repoMap['IrhpPermitRange']
            ->shouldReceive('save')
            ->once()
            ->with($entity);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => ['Irhp Permit Range' => $id],
            'messages' => ["Irhp Permit Range '" . $id . "' updated"]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Test for overlapping IRHP Permit Ranges - no values are asserted as this tests to ensure that a validation
     * exception is thrown.
     */
    public function testHandleOverlap()
    {
        $this->expectException(\Dvsa\Olcs\Api\Domain\Exception\ValidationException::class);
        $cmdData = [
            'irhpPermitStock' => '1',
            'emissionsCategory' => RefData::EMISSIONS_CATEGORY_EURO6_REF,
            'prefix' => 'UK',
            'fromNo' => '1',
            'toNo' => '100',
            'isReserve' => '0',
            'isReplacement' => '0',
            'countrys' => []
        ];

        $entity = m::mock(PermitRangeEntity::class);

        $command = UpdateCmd::create($cmdData);

        $this->repoMap['IrhpPermitRange']
            ->shouldReceive('fetchUsingId')
            ->once()
            ->with($command)
            ->andReturn($entity);

        $this->repoMap['IrhpPermitRange']
            ->shouldReceive('findOverlappingRangesByType')
            ->with(
                $cmdData['irhpPermitStock'],
                $cmdData['fromNo'],
                $cmdData['toNo'],
                $entity
            )
            ->andReturn([m::mock(PermitRangeEntity::class)]);

        $this->sut->handleCommand($command);
    }

    /**
     * @dataProvider dpShortTermAnnualTypeCombinations
     */
    public function testHandleCommandBadEcmtEmissionsCategory($isEcmtShortTerm, $isEcmtAnnual)
    {
        $this->expectException(\Dvsa\Olcs\Api\Domain\Exception\ValidationException::class);
        $cmdData = [
            'irhpPermitStock' => '1',
            'emissionsCategory' => RefData::EMISSIONS_CATEGORY_NA_REF,
            'prefix' => 'UK',
            'fromNo' => '1',
            'toNo' => '100',
            'isReserve' => '0',
            'isReplacement' => '0',
            'countrys' => []
        ];

        $command = UpdateCmd::create($cmdData);

        $irhpPermitType = m::mock(IrhpPermitType::class);
        $irhpPermitType->shouldReceive('isEcmtShortTerm')->andReturn($isEcmtShortTerm);
        $irhpPermitType->shouldReceive('isEcmtAnnual')->andReturn($isEcmtAnnual);

        $irhpPermitStock = m::mock(IrhpPermitStock::class);
        $irhpPermitStock->shouldReceive('getIrhpPermitType')->andReturn($irhpPermitType);

        $this->repoMap['IrhpPermitStock']
            ->shouldReceive('fetchById')
            ->andReturn($irhpPermitStock);

        $entity = m::mock(PermitRangeEntity::class);

        $this->repoMap['IrhpPermitRange']
            ->shouldReceive('fetchUsingId')
            ->once()
            ->with($command)
            ->andReturn($entity);

        $this->repoMap['IrhpPermitRange']
            ->shouldReceive('findOverlappingRangesByType')
            ->with(
                $cmdData['irhpPermitStock'],
                $cmdData['fromNo'],
                $cmdData['toNo'],
                $entity
            )
            ->andReturn([]);

        $this->sut->handleCommand($command);
    }

    public function dpShortTermAnnualTypeCombinations()
    {
        return [
            [true, false],
            [false, true],
        ];
    }
}
