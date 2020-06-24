<?php

/**
 * Create Conviction Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Conviction;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Conviction\Delete as DeleteCommandHandler;
use Dvsa\Olcs\Transfer\Command\Cases\Conviction\Delete as DeleteCommand;
use Dvsa\Olcs\Api\Domain\Repository\Conviction;
use Dvsa\Olcs\Api\Entity\Cases\Conviction as ConvictionEntity;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

use Dvsa\Olcs\Api\Entity;

/**
 * Create Conviction Test
 */
class DeleteTest extends CommandHandlerTestCase
{
    /**
     * @var DeleteCommandHandler
     */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new DeleteCommandHandler();
        $this->mockRepo('Conviction', Conviction::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $id = 111;

        $data = [
            'id' => $id,
            'version' => 2
        ];

        $command = DeleteCommand::create($data);

        /** @var ConvictionEntity $impounding */
        $noteEntity = m::mock(ConvictionEntity::class)->makePartial();
        $noteEntity->setId($command->getId());

        /** @var $conviction ConvictionEntity */
        $conviction = null;

        $this->repoMap['Conviction']->shouldReceive('fetchById')
            ->with($id)
            ->andReturn($noteEntity)
            ->shouldReceive('delete')
            ->with(m::type(ConvictionEntity::class))
            ->andReturnUsing(
                function (ConvictionEntity $ce) use (&$conviction) {
                    $conviction = $ce;
                    $conviction->setId(111);
                }
            )
            ->once();

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf('Dvsa\Olcs\Api\Domain\Command\Result', $result);
        $this->assertObjectHasAttribute('ids', $result);
        $this->assertObjectHasAttribute('messages', $result);
        $this->assertContains('Id 111 deleted', $result->getMessages());
    }
}
