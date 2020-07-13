<?php

/**
 * Create Prohibition\Defect Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Prohibition\Defect;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Prohibition\Defect\Delete as DeleteCommandHandler;
use Dvsa\Olcs\Transfer\Command\Cases\Prohibition\Defect\Delete as DeleteCommand;
use Dvsa\Olcs\Api\Domain\Repository\ProhibitionDefect;
use Dvsa\Olcs\Api\Entity\Prohibition\ProhibitionDefect as ProhibitionDefectEntity;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

use Dvsa\Olcs\Api\Entity;

/**
 * Create Prohibition Defect Delete Test
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
        $this->mockRepo('ProhibitionDefect', ProhibitionDefect::class);

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

        /** @var ProhibitionDefectEntity $impounding */
        $noteEntity = m::mock(ProhibitionDefectEntity::class)->makePartial();
        $noteEntity->setId($command->getId());

        /** @var $conviction ProhibitionDefectEntity */
        $conviction = null;

        $this->repoMap['ProhibitionDefect']->shouldReceive('fetchById')
            ->with($id)
            ->andReturn($noteEntity)
            ->shouldReceive('delete')
            ->with(m::type(ProhibitionDefectEntity::class))
            ->andReturnUsing(
                function (ProhibitionDefectEntity $ce) use (&$conviction) {
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
