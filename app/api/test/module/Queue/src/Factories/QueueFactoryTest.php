<?php

namespace OlcsTest\Queue\Factories;

use Aws\Sqs\SqsClient;
use Dvsa\Olcs\Queue\Factories\QueueFactory;
use Dvsa\Olcs\Queue\Service\Queue;
use OlcsTest\Bootstrap;
use PHPUnit\Framework\TestCase;

class QueueFactoryTest extends TestCase
{
    protected $sm;

    protected $sut;

    public function setUp(): void
    {
        $this->sut = new QueueFactory();

        $this->sm = Bootstrap::getServiceManager();
    }

    public function testCreateService()
    {
        $sqsClient = \Mockery::mock(SqsClient::class);
        // Mocks
        $this->sm->setService('SqsClient', $sqsClient);

        /**
         * @var SqsClient $sqsClient
         */

        $this->assertInstanceOf(Queue::class, $this->sut->createService($this->sm));
    }
}
