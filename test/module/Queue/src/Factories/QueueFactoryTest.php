<?php

namespace OlcsTest\Queue\Factories;

use Aws\Sqs\SqsClient;
use Dvsa\Olcs\Queue\Factories\QueueFactory;
use Dvsa\Olcs\Queue\Service\Queue;
use Laminas\ServiceManager\ServiceManager;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;

class QueueFactoryTest extends TestCase
{
    protected $sm;

    protected $sut;

    public function setUp(): void
    {
        $this->sut = new QueueFactory();

        $sm = m::mock(ServiceManager::class);

        $sm->shouldReceive('setService')
            ->andReturnUsing(
                function ($alias, $service) use ($sm) {
                    $sm->shouldReceive('get')->with($alias)->andReturn($service);
                    $sm->shouldReceive('has')->with($alias)->andReturn(true);
                    return $sm;
                }
            );

        $this->sm = $sm;
    }

    public function testCreateService()
    {
        $sqsClient = \Mockery::mock(SqsClient::class);
        // Mocks
        $this->sm->setService('SqsClient', $sqsClient);

        /**
         * @var SqsClient $sqsClient
         */

        $this->assertInstanceOf(Queue::class, $this->sut->__invoke($this->sm, Queue::class));
    }
}
