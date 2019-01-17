<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler;

use Doctrine\ORM\Query;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Mockery as m;

abstract class AbstractUpdateCommandHandlerTest extends CommandHandlerTestCase
{
    protected $repoServiceName = 'changeMe';
    protected $commandMethodName = 'changeMe';
    protected $entityMethodName = 'changeMe';
    protected $entityClass = 'changeMe';

    private $commandValue = 'commandValue';

    public function setUp()
    {
        $this->mockRepo($this->repoServiceName, $this->entityClass);
        $this->sut = new $this->sutClass();
     
        parent::setUp();
    }

    protected function initReferences()
    {
        if ($this->sut->isRefData()) {
            $this->refData = [
                $this->commandValue
            ];
        }

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $entityId = 43;

        $entity = m::mock($this->entityClass);
        $entity->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn($entityId);

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn($entityId);
        $command->shouldReceive($this->commandMethodName)
            ->withNoArgs()
            ->andReturn($this->commandValue);

        $this->repoMap[$this->repoServiceName]->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT)
            ->once()
            ->andReturn($entity);

        if ($this->sut->isRefData()) {
            $entity->shouldReceive($this->entityMethodName)
                ->with($this->refData[$this->commandValue])
                ->once()
                ->ordered()
                ->globally();
        } else {
            $entity->shouldReceive($this->entityMethodName)
                ->with($this->commandValue)
                ->once()
                ->ordered()
                ->globally();
        }

        $this->repoMap[$this->repoServiceName]->shouldReceive('save')
            ->with($entity)
            ->once()
            ->ordered()
            ->globally();

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            [$this->repoServiceName . ' updated'],
            $result->getMessages()
        );

        $this->assertEquals(
            $entityId,
            $result->getId($this->repoServiceName)
        );
    }
}
