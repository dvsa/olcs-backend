<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpPermitApplication;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitApplication\Delete as DeleteCommandHandler;
use Dvsa\Olcs\Api\Domain\Command\IrhpPermitApplication\Delete as DeleteCommand;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication as IrhpPermitApplicationEntity;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

/**
 * Delete IrhpPermitApplication Test
 */
class DeleteTest extends CommandHandlerTestCase
{
    /**
     * @var DeleteCommandHandler
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new DeleteCommandHandler();
        $this->mockRepo('IrhpPermitApplication', IrhpPermitApplication::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $id = 111;

        $data = [
            'id' => $id,
        ];

        $command = DeleteCommand::create($data);

        /** @var IrhpPermitApplicationEntity $irhpPermitApplicationEntity */
        $irhpPermitApplicationEntity = m::mock(IrhpPermitApplicationEntity::class)->makePartial();
        $irhpPermitApplicationEntity->setId($command->getId());

        /** @var $e IrhpPermitApplicationEntity */
        $e = null;

        $this->repoMap['IrhpPermitApplication']->shouldReceive('fetchById')
            ->with($id)
            ->andReturn($irhpPermitApplicationEntity)
            ->shouldReceive('delete')
            ->with(m::type(IrhpPermitApplicationEntity::class))
            ->andReturnUsing(
                function (IrhpPermitApplicationEntity $ce) use (&$e) {
                    $e = $ce;
                    $e->setId(111);
                }
            )
            ->once();

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);
        $this->assertObjectHasAttribute('ids', $result);
        $this->assertObjectHasAttribute('messages', $result);
        $this->assertContains('Id 111 deleted', $result->getMessages());
    }
}
