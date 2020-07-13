<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpPermitWindow;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitWindow\Delete as DeleteHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as PermitStockRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitWindow as PermitWindowRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\IrhpPermitWindow\Delete as DeleteCmd;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow as PermitWindowEntity;

/**
 * Delete IRHP Permit Window
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
class DeleteTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new DeleteHandler;
        $this->mockRepo('IrhpPermitWindow', PermitWindowRepo::class);
        $this->mockRepo('IrhpPermitStock', PermitStockRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $cmdData = [
            'id' => '1'
        ];

        $command = DeleteCmd::create($cmdData);

        $id = $command->getId();

        $irhpPermitWindow = m::mock(PermitWindowEntity::class);

        $this->repoMap['IrhpPermitWindow']
            ->shouldReceive('fetchById')
            ->with($id)
            ->once()
            ->andReturn($irhpPermitWindow);

        $irhpPermitWindow->shouldReceive('canBeDeleted')->once()->andReturn(true);


        $this->repoMap['IrhpPermitWindow']
            ->shouldReceive('delete')
            ->with($irhpPermitWindow);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => ['id' => 1],
            'messages' => ['Permit Window Deleted']
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
