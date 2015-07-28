<?php

/**
 * Create NonPi Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\NonPi;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\NonPi\Delete as DeleteCommandHandler;
use Dvsa\Olcs\Transfer\Command\Cases\NonPi\Delete as DeleteCommand;
use Dvsa\Olcs\Api\Domain\Repository\NonPi;
use Dvsa\Olcs\Api\Entity\Cases\Hearing as NonPiEntity;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

use Dvsa\Olcs\Api\Entity;

/**
 * Create NonPi Test
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
        $this->mockRepo('NonPi', NonPi::class);

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

        /** @var NonPiEntity $impounding */
        $noteEntity = m::mock(NonPiEntity::class)->makePartial();
        $noteEntity->setId($command->getId());

        /** @var $e NonPiEntity */
        $e = null;

        $this->repoMap['NonPi']->shouldReceive('fetchById')
            ->with($id)
            ->andReturn($noteEntity)
            ->shouldReceive('delete')
            ->with(m::type(NonPiEntity::class))
            ->andReturnUsing(
                function (NonPiEntity $ce) use (&$e) {
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
