<?php

namespace OlcsTest\Queue\Factories;

use Dvsa\Olcs\Queue\Factories\MessageBuilderFactory;
use Dvsa\Olcs\Queue\Service\Message\MessageBuilder;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;
use Mockery as m;

class MessageBuilderFactoryTest extends TestCase
{
    protected $mockSl;
    protected $sut;

    public function setUp(): void
    {
        $this->mockSl = m::mock(ContainerInterface::class);
        $this->sut = new MessageBuilderFactory();
    }

    public function testCreateService()
    {
        $this->assertInstanceOf(MessageBuilder::class, $this->sut->__invoke($this->mockSl, MessageBuilder::class));
    }
}
