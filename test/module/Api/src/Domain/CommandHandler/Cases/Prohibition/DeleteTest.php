<?php

/**
 * Create Prohibition Test
 */

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Prohibition;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Prohibition\Delete as DeleteCommandHandler;
use Dvsa\Olcs\Transfer\Command\Cases\Prohibition\Delete as DeleteCommand;
use Dvsa\Olcs\Api\Domain\Repository\Prohibition;
use Dvsa\Olcs\Api\Entity\Prohibition\Prohibition as ProhibitionEntity;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Entity;

/**
 * Create Prohibition Test
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
        $this->mockRepo('Prohibition', Prohibition::class);

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

        /** @var ProhibitionEntity $impounding */
        $noteEntity = m::mock(ProhibitionEntity::class)->makePartial();
        $noteEntity->setId($command->getId());

        /** @var $conviction ProhibitionEntity */
        $conviction = null;

        $this->repoMap['Prohibition']->shouldReceive('fetchById')
            ->with($id)
            ->andReturn($noteEntity)
            ->shouldReceive('delete')
            ->with(m::type(ProhibitionEntity::class))
            ->andReturnUsing(
                function (ProhibitionEntity $ce) use (&$conviction) {
                    $conviction = $ce;
                    $conviction->setId(111);
                }
            )
            ->once();

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(\Dvsa\Olcs\Api\Domain\Command\Result::class, $result);
        $this->assertTrue(property_exists($result, 'ids'));
        $this->assertTrue(property_exists($result, 'messages'));
        $this->assertContains('Id 111 deleted', $result->getMessages());
    }
}
