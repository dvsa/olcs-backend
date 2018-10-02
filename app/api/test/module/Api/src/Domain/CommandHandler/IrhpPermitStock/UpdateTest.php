<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpPermitStock;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitStock\Update as UpdateHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as PermitStockRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitType as PermitTypeRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\IrhpPermitStock\Update as UpdateCmd;
use Dvsa\Olcs\Api\Entity\System\IrhpPermitStock as PermitStockEntity;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;

/**
 * Update IrhpPermitStock Test
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class UpdateTest extends CommandHandlerTestCase
{
    use ProcessDateTrait;

    public function setUp()
    {
        $this->sut = new UpdateHandler();
        $this->mockRepo('IrhpPermitStock', PermitStockRepo::class);
        $this->mockRepo('IrhpPermitType', PermitTypeRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $id = 1;
        $permitType = '1';
        $validFrom = '2019-01-01';
        $validTo = '2019-01-01';
        $initialStock = '1500';

        $cmdData = [
            'permitType' => $permitType,
            'validFrom' => $validFrom,
            'validTo' => $validTo,
            'initialStock' => $initialStock
        ];

        $command = UpdateCmd::create($cmdData);

        $entity = m::mock(PermitStockEntity::class);

        $entity->shouldReceive('update')
            ->andReturn(m::mock(IrhpPermitStock::class));

        $this->repoMap['IrhpPermitType']
            ->shouldReceive('fetchById')
            ->with('1')
            ->andReturn(m::mock(IrhpPermitType::class)->makePartial());

        $entity->shouldReceive('getId')
            ->twice()
            ->andReturn($id);

        $this->repoMap['IrhpPermitStock']
            ->shouldReceive('fetchUsingId')
            ->once()
            ->with($command)
            ->andReturn($entity);

        $this->repoMap['IrhpPermitStock']
            ->shouldReceive('save')
            ->once()
            ->with(m::type(PermitStockEntity::class));

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => ['Irhp Permit Stock' => $id],
            'messages' => ["Irhp Permit Stock '" . $id . "' updated"]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
