<?php

/**
 * Delete Statement Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Statement;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Statement\DeleteStatement as DeleteCommandHandler;
use Dvsa\Olcs\Transfer\Command\Cases\Statement\DeleteStatement as DeleteCommand;
use Dvsa\Olcs\Api\Domain\Repository\Statement;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\Cases\Statement as StatementEntity;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

use Dvsa\Olcs\Api\Entity;

/**
 * Delete Statement Test
 */
class DeleteStatementTest extends CommandHandlerTestCase
{
    /**
     * @var DeleteCommandHandler
     */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new DeleteCommandHandler();
        $this->mockRepo('Statement', Statement::class);
        $this->mockRepo('Document', Repository\Document::class);

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

        /** @var StatementEntity $statementEntity */
        $statementEntity = m::mock(StatementEntity::class)->makePartial();
        $statementEntity->setId($command->getId());

        $this->repoMap['Statement']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT)
            ->andReturn($statementEntity)
            ->shouldReceive('delete')
            ->with(m::type(StatementEntity::class))
            ->once();

        $this->repoMap['Document']->shouldReceive('fetchListForStatement')->with($id)->once()->andReturn(
            ['DOCUMENT1', 'DOCUMENT2']
        );
        $this->repoMap['Document']->shouldReceive('delete')->with('DOCUMENT1')->once();
        $this->repoMap['Document']->shouldReceive('delete')->with('DOCUMENT2')->once();

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf('Dvsa\Olcs\Api\Domain\Command\Result', $result);
        $this->assertObjectHasAttribute('ids', $result);
        $this->assertObjectHasAttribute('messages', $result);
        $this->assertContains('Statement deleted', $result->getMessages());
    }
}
