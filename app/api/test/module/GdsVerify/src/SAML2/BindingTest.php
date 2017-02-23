<?php

namespace Dvsa\OlcsTest\GdsVerify\Data;

use Dvsa\Olcs\GdsVerify\SAML2\Binding;
use Mockery as m;

/**
 * Binding test
 */
class BindingTest extends \PHPUnit_Framework_TestCase
{
    public function testSend()
    {
        $doc = new \DOMDocument();
        $element = new \DOMElement('foo', 'bar');
        $doc->appendChild($element);

        $message = m::mock(\SAML2\Message::class);
        $message->shouldReceive('toSignedXML')->with()->once()->andReturn($element);
        $message->shouldReceive('getDestination')->with()->once()->andReturn('DESTINATION');

        $sut = new Binding();

        $post = $sut->send($message);

        $this->assertSame(['url' => 'DESTINATION', 'samlRequest' => 'PGZvbz5iYXI8L2Zvbz4='], $post);
    }

    public function testReceive()
    {
        $sut = new Binding();
        $this->assertNull($sut->receive());
    }

    public function testProcessResponse()
    {
        // Difficult to test
    }
}
