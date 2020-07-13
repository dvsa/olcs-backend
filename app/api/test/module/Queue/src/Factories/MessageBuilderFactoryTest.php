<?php

namespace OlcsTest\Queue\Factories;

use Dvsa\Olcs\Queue\Factories\MessageBuilderFactory;
use Dvsa\Olcs\Queue\Service\Message\MessageBuilder;
use PHPUnit\Framework\TestCase;
use Mockery as m;
use Zend\ServiceManager\ServiceLocatorInterface;

class MessageBuilderFactoryTest extends TestCase
{
    protected $mockSl;
    protected $sut;

    public function setUp(): void
    {
        $this->mockSl = m::mock(ServiceLocatorInterface::class);
        $this->sut = new MessageBuilderFactory();
    }
    
    public function testCreateService()
    {
        $this->assertInstanceOf(MessageBuilder::class, $this->sut->createService($this->mockSl));
    }
}
