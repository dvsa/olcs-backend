<?php

/**
 * Queue Processor Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Cli\Service\Queue;

use Doctrine\DBAL\DBALException;
use Dvsa\Olcs\Api\Domain\Query\Queue\NextItem as NextQueueItemQry;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\MessageConsumerInterface;
use Dvsa\Olcs\Cli\Service\Queue\MessageConsumerManager;
use Dvsa\Olcs\Cli\Service\Queue\QueueProcessor;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use OlcsTest\Bootstrap;

/**
 * Queue Processor Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class QueueProcessorTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp(): void
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new QueueProcessor();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testProcessNextItemWithoutItem()
    {
        $includeTypes = ['foo'];
        $excludeTypes = ['bar'];

        // Mocks
        $mockQueryHandlerManager = m::mock();
        $this->sm->setService('QueryHandlerManager', $mockQueryHandlerManager);

        // Expectations
        $this->expectQuery(
            $mockQueryHandlerManager,
            NextQueueItemQry::class,
            ['includeTypes' => $includeTypes, 'excludeTypes' => $excludeTypes],
            null
        );

        // Assertions
        $this->assertNull($this->sut->processNextItem($includeTypes, $excludeTypes));
    }

    public function testProcessNextItem()
    {
        $includeTypes = ['foo'];
        $excludeTypes = ['bar'];

        $type = new RefData($includeTypes[0]);
        $item = new QueueEntity($type);

        // Mocks
        $mockQueryHandlerManager = m::mock();
        $mockMsm = m::mock(MessageConsumerManager::class)->makePartial();
        $this->sm->setService('QueryHandlerManager', $mockQueryHandlerManager);
        $this->sm->setService('MessageConsumerManager', $mockMsm);
        $mockConsumer = m::mock(MessageConsumerInterface::class);
        $mockMsm->setService('foo', $mockConsumer);

        // Expectations
        $this->expectQuery(
            $mockQueryHandlerManager,
            NextQueueItemQry::class,
            ['includeTypes' => $includeTypes, 'excludeTypes' => $excludeTypes],
            $item
        );

        $mockConsumer->shouldReceive('processMessage')
            ->once()
            ->with($item)
            ->andReturn('foo');

        // Assertions
        $this->assertEquals('foo', $this->sut->processNextItem($includeTypes, $excludeTypes));
    }

    public function testProcessMessageHandlesException()
    {
        $includeTypes = ['foo'];
        $excludeTypes = ['bar'];

        $type = new RefData($includeTypes[0]);
        $item = new QueueEntity($type);

        // Mocks
        $mockQueryHandlerManager = m::mock();
        $mockMsm = m::mock(MessageConsumerManager::class)->makePartial();
        $this->sm->setService('QueryHandlerManager', $mockQueryHandlerManager);
        $this->sm->setService('MessageConsumerManager', $mockMsm);
        $mockConsumer = m::mock(MessageConsumerInterface::class);
        $mockMsm->setService('foo', $mockConsumer);

        // Expectations
        $this->expectQuery(
            $mockQueryHandlerManager,
            NextQueueItemQry::class,
            ['includeTypes' => $includeTypes, 'excludeTypes' => $excludeTypes],
            $item
        );

        $exceptionMessage = 'something went wrong';
        $mockConsumer->shouldReceive('processMessage')
            ->once()
            ->with($item)
            ->andThrow(new \Exception($exceptionMessage))
            ->shouldReceive('failed')
            ->once()
            ->with($item, $exceptionMessage)
            ->andReturn('error message');

        // Assertions
        $this->assertEquals('error message', $this->sut->processNextItem($includeTypes, $excludeTypes));
    }

    public function testProcessMessageHandlesOrmException()
    {
        $includeTypes = ['foo'];
        $excludeTypes = ['bar'];

        $type = new RefData($includeTypes[0]);
        $item = new QueueEntity($type);

        // Mocks
        $mockQueryHandlerManager = m::mock();
        $mockMsm = m::mock(MessageConsumerManager::class)->makePartial();
        $this->sm->setService('QueryHandlerManager', $mockQueryHandlerManager);
        $this->sm->setService('MessageConsumerManager', $mockMsm);
        $mockConsumer = m::mock(MessageConsumerInterface::class);
        $mockMsm->setService('foo', $mockConsumer);

        // Expectations
        $this->expectQuery(
            $mockQueryHandlerManager,
            NextQueueItemQry::class,
            ['includeTypes' => $includeTypes, 'excludeTypes' => $excludeTypes],
            $item
        );

        $exceptionMessage = 'something went wrong';
        $mockConsumer->shouldReceive('processMessage')
            ->once()
            ->with($item)
            ->andThrow(new \Doctrine\ORM\ORMException($exceptionMessage));

        $this->expectException(\Doctrine\ORM\ORMException::class);

        $this->sut->processNextItem($includeTypes, $excludeTypes);
    }

    public function testProcessMessageHandlesDbalException()
    {
        $includeTypes = ['foo'];
        $excludeTypes = ['bar'];

        $type = new RefData($includeTypes[0]);
        $item = new QueueEntity($type);

        // Mocks
        $mockQueryHandlerManager = m::mock();
        $mockMsm = m::mock(MessageConsumerManager::class)->makePartial();
        $this->sm->setService('QueryHandlerManager', $mockQueryHandlerManager);
        $this->sm->setService('MessageConsumerManager', $mockMsm);
        $mockConsumer = m::mock(MessageConsumerInterface::class);
        $mockMsm->setService('foo', $mockConsumer);

        // Expectations
        $this->expectQuery(
            $mockQueryHandlerManager,
            NextQueueItemQry::class,
            ['includeTypes' => $includeTypes, 'excludeTypes' => $excludeTypes],
            $item
        );

        $exceptionMessage = 'something went wrong';
        $mockConsumer->shouldReceive('processMessage')
            ->once()
            ->with($item)
            ->andThrow(new DBALException($exceptionMessage));

        $this->expectException(DBALException::class);

        $this->sut->processNextItem($includeTypes, $excludeTypes);
    }

    /**
     * @param QueryHandlerManager $queryHandlerManager service mock
     * @param string $class expected Query/Command class name
     * @param array $expectedDtoData
     * @param array $result to be returned by $response->getResult()
     */
    private function expectQuery($queryHandlerManager, $class, $expectedDtoData, $result)
    {
        $queryHandlerManager
            ->shouldReceive('handleQuery')
            ->with(
                m::on(
                    function ($cmd) use ($expectedDtoData, $class) {
                        $matched = (
                            is_a($cmd, $class)
                            &&
                            $cmd->getArrayCopy() == $expectedDtoData
                        );
                        return $matched;
                    }
                )
            )
            ->once()
            ->andReturn($result);
    }
}
