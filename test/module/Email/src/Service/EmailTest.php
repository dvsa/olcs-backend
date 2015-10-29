<?php

/**
 * Email Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Email\Service;

use Dvsa\Olcs\Email\Service\Email;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Mail\Message;
use Zend\Mime\Part;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mail\Transport\TransportInterface;

/**
 * Email Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class EmailTest extends MockeryTestCase
{
    /**
     * @var Email
     */
    private $sut;

    public function setUp()
    {
        $this->sut = new Email();
    }

    public function testCreateServiceMissingConfig()
    {
        $this->setExpectedException(\Zend\Mail\Exception\RuntimeException::class);

        $config = [];

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get')->with('Config')->andReturn($config);

        $this->sut->createService($sm);
    }

    public function testCreateService()
    {
        $config = [
            'mail' => []
        ];

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get')->with('Config')->andReturn($config);

        $service = $this->sut->createService($sm);

        $this->assertSame($this->sut, $service);

        $transport = $this->sut->getMailTransport();

        $this->assertInstanceOf(TransportInterface::class, $transport);
    }

    public function testSendHtml()
    {
        $transport = m::mock(TransportInterface::class);

        $this->sut->setMailTransport($transport);

        $transport->shouldReceive('send')
            ->once()
            ->with(m::type(Message::class))
            ->andReturnUsing(
                function (Message $message) {

                    $content = $message->toString();

                    $parts = explode("\r\n", $content);

                    array_shift($parts);

                    $expected = [
                        'From: foo <foo@bar.com>',
                        'To: bar@foo.com',
                        'Subject: Subject',
                        'MIME-Version: 1.0',
                        'Content-Type: text/html',
                        'Content-Transfer-Encoding: 8bit',
                        '',
                        'This is the content'
                    ];

                    $this->assertEquals($expected, $parts);
                }
            );

        $this->sut->send('foo@bar.com', 'foo', 'bar@foo.com', 'Subject', 'This is the content', true);
    }

    public function testSend()
    {
        $transport = m::mock(TransportInterface::class);

        $this->sut->setMailTransport($transport);

        $transport->shouldReceive('send')
            ->once()
            ->with(m::type(Message::class))
            ->andReturnUsing(
                function (Message $message) {

                    $content = $message->toString();

                    $parts = explode("\r\n", $content);

                    array_shift($parts);

                    $expected = [
                        'From: foo <foo@bar.com>',
                        'To: bar@foo.com',
                        'Subject: Subject',
                        '',
                        'This is the content'
                    ];

                    $this->assertEquals($expected, $parts);
                }
            );

        $this->sut->send('foo@bar.com', 'foo', 'bar@foo.com', 'Subject', 'This is the content', false);
    }
}
