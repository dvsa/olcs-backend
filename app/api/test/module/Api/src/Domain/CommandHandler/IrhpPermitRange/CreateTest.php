<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpPermitRange;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitRange\Create as CreateHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitRange as PermitRangeRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as PermitStockRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\IrhpPermitRange\Create as CreateCmd;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange as PermitRangeEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;

/**
 * Create IRHP Permit Range Test
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
class CreateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateHandler();
        $this->mockRepo('IrhpPermitRange', PermitRangeRepo::class);
        $this->mockRepo('IrhpPermitStock', PermitStockRepo::class);

        parent::setUp();
    }

    /**
     * Test the Happy Path
     */
    public function testHandleCommand()
    {
        $cmdData = [
            'irhpPermitStock' => '1',
            'prefix' => 'UK',
            'fromNo' => '1',
            'toNo' => '100',
            'isReserve' => '0',
            'isReplacement' => '0',
            'countrys' => []
        ];

        $command = CreateCmd::create($cmdData);

        $this->repoMap['IrhpPermitStock']
            ->shouldReceive('fetchById')
            ->andReturn(m::mock(IrhpPermitStock::class)->makePartial());

        $this->repoMap['IrhpPermitRange']->shouldReceive('findOverlappingRangesByType')
            ->andReturn([]);

        $this->repoMap['IrhpPermitRange']
            ->shouldReceive('save')
            ->once()
            ->with(m::type(PermitRangeEntity::class))
            ->andReturnUsing(
                function (PermitRangeEntity $permitRange) use (&$savedPermitRange) {
                    $permitRange->setId(1);
                    $savedPermitRange = $permitRange;
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => ['IrhpPermitRange' => 1],
            'messages' => ["IRHP Permit Range '1' created"]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    /**
    * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ValidationException
    *
    * Test for overlapping IRHP Permit Ranges - no values are asserted as this tests to ensure that a validation
    * exception is thrown.
    */
    public function testHandleOverlap()
    {
        $cmdData = [
            'irhpPermitStock' => '1',
            'prefix' => 'UK',
            'fromNo' => '1',
            'toNo' => '100',
            'isReserve' => '0',
            'isReplacement' => '0',
            'countrys' => []
        ];

        $command = CreateCmd::create($cmdData);

        $this->repoMap['IrhpPermitStock']
            ->shouldReceive('fetchById')
            ->andReturn(m::mock(IrhpPermitStock::class));

        $this->repoMap['IrhpPermitRange']->shouldReceive('findOverlappingRangesByType')
            ->andReturn(['overlappingPermitRange']);

        $this->sut->handleCommand($command);
    }
}
