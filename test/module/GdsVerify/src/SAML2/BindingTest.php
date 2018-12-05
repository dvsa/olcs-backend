<?php

namespace Dvsa\OlcsTest\GdsVerify\Data;

use Dvsa\Olcs\GdsVerify\SAML2\Binding;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

/**
 * Binding test
 */
class BindingTest extends TestCase
{
    public function testSend()
    {
        $doc = new \DOMDocument();
        $element = new \DOMElement('foo', 'bar');
        $doc->appendChild($element);

        $message = m::mock(\SAML2\Message::class);
        $message->shouldReceive('toSignedXML')->with()->once()->andReturn($element);
        $message->shouldReceive('getDestination')->with()->once()->andReturn('DESTINATION');

        $mockContainer = m::mock(\SAML2\Compat\AbstractContainer::class);
        $mockContainer->shouldReceive('debugMessage')->once();
        \SAML2\Compat\ContainerSingleton::setContainer($mockContainer);

        $sut = new Binding();

        $post = $sut->send($message);

        $this->assertSame(['url' => 'DESTINATION', 'samlRequest' => 'PGZvbz5iYXI8L2Zvbz4='], $post);
    }

    public function testReceive()
    {
        // Nothing to test (is No op)
    }

    public function testProcessResponse()
    {
        // Difficult to test
    }
}
