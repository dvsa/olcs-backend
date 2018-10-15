<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpPermitRange;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitRange\Update as UpdateHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitRange as PermitRangeRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as PermitStockRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\IrhpPermitRange\Update as UpdateCmd;
use Dvsa\Olcs\Api\Entity\System\IrhpPermitRange as PermitRangeEntity;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;

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

    public function testHandleCommand()
    {
        $id = 1;
        $cmdData = [
            'irhpPermitStock' => '1',
            'prefix' => 'UK',
            'fromNo' => 1,
            'toNo' => 100,
            'ssReserve' => 0,
            'lostReplacement' => 0,
            'countrys' => []
        ];

        $stock = m::mock(IrhpPermitStock::class)->makePartial();

        $command = UpdateCmd::create($cmdData);

        $entity = m::mock(PermitRangeEntity::class);

        $this->repoMap['IrhpPermitStock']
            ->shouldReceive('fetchById')
            ->with($command->getIrhpPermitStock())
            ->andReturn($stock);

        $entity->shouldReceive('update')
            ->with($stock, 'UK', '1', '100', '0', '0', [])
            ->andReturn(m::mock(IrhpPermitRange::class));

        $entity->shouldReceive('getId')
            ->twice()
            ->andReturn($id);

        $this->repoMap['IrhpPermitRange']
            ->shouldReceive('fetchUsingId')
            ->once()
            ->with($command)
            ->andReturn($entity);

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
}
