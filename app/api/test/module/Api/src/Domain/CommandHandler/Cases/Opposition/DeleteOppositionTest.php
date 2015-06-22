<?php

/**
 * Create Opposition Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Opposition;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Opposition\DeleteOpposition as DeleteCommandHandler;
use Dvsa\Olcs\Transfer\Command\Cases\Opposition\DeleteOpposition as DeleteCommand;
use Dvsa\Olcs\Api\Domain\Repository\Opposition;
use Dvsa\Olcs\Api\Entity\Opposition\Opposition as OppositionEntity;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

use Dvsa\Olcs\Api\Entity;

/**
 * Create Opposition Test
 */
class DeleteOppositionTest extends CommandHandlerTestCase
{
    /**
     * @var DeleteCommandHandler
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new DeleteCommandHandler();
        $this->mockRepo('Opposition', Opposition::class);

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

        /** @var OppositionEntity $oppositionEntity */
        $oppositionEntity = m::mock(OppositionEntity::class)->makePartial();
        $oppositionEntity->setId($command->getId());

        /** @var OppositionEntity $opposition */
        $opposition = null;

        $this->repoMap['Opposition']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, $command->getVersion())
            ->andReturn($oppositionEntity)
            ->shouldReceive('delete')
            ->with(m::type(OppositionEntity::class))
            ->andReturnUsing(
                function (OppositionEntity $oe) use (&$opposition) {
                    $opposition = $oe;
                    $opposition->setId(111);
                }
            )
            ->once();

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf('Dvsa\Olcs\Api\Domain\Command\Result', $result);
        $this->assertObjectHasAttribute('ids', $result);
        $this->assertObjectHasAttribute('messages', $result);
        $this->assertContains('Deleted', $result->getMessages());
    }
}
