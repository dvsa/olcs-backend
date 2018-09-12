<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpPermitWindow;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitWindow\Update as UpdateHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as PermitStockRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitWindow as PermitWindowRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\IrhpPermitWindow\Update as UpdateCmd;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow as PermitWindowEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow as PermitStockEntity;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;

/**
 * Update IrhpPermitStock Test
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
class UpdateTest extends CommandHandlerTestCase
{
    use ProcessDateTrait;

    public function setUp()
    {
        $this->sut = new UpdateHandler();
        $this->mockRepo('IrhpPermitWindow', PermitWindowRepo::class);
        $this->mockRepo('IrhpPermitStock', PermitStockRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {

        $cmdData = [
            'id' => 1,
            'irhpPermitStock' => 1,
            'startDate' => '2019-12-01',
            'endDate' => '2019-12-30',
            'daysForPayment' => 14
        ];

        $command = UpdateCmd::create($cmdData);

        $permitWindowEntity = m::mock(PermitWindowEntity::class)->makePartial();

        $permitWindowEntity->shouldReceive('isActive')->once()->andReturn(false);
        $permitWindowEntity->shouldReceive('hasEnded')->once()->andReturn(false);

        $this->repoMap['IrhpPermitWindow']
            ->shouldReceive('fetchById')
            ->with('1')
            ->andReturn($permitWindowEntity);

        $permitWindowEntity->shouldReceive('getId')
            ->twice()
            ->andReturn($command->getId());



        $this->repoMap['IrhpPermitWindow']
            ->shouldReceive('findOverlappingWindowsByType')
            ->once()
            ->andReturn([]);

        $permitWindowEntity->shouldReceive('update')
            ->andReturn($permitWindowEntity);


        $permitWindowEntity->shouldReceive('processDate');

        $this->repoMap['IrhpPermitStock']
            ->shouldReceive('fetchById')
            ->once()
            ->with($command->getIrhpPermitStock())
            ->andReturn($permitWindowEntity);

        $this->repoMap['IrhpPermitWindow']
            ->shouldReceive('save')
            ->once()
            ->with(m::type(PermitWindowEntity::class));

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => ['Irhp Permit Window' => $command->getId()],
            'messages' => ["Irhp Permit Window '" . $command->getId() . "' updated"]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
