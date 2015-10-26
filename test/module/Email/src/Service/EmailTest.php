<?php
namespace Olcs\Email\Service;

use Olcs\Email\Service\Email as EmailService;
use Zend\Mail\Transport\Null as NullTransport;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

/**
 * EmailTest
 */
class EmailTest extends TestCase
{
    private $mailService;

    protected function setUp()
    {
        $this->mailService = new EmailService();
    }

    public function testSendWithNullAdapter()
    {
        $transport = new NullTransport();

        $this->mailService->setMailTransport($transport);

        $data = [
            'fromEmail' => 'some@email.com',
            'fromName'  => 'Fred Smith',
            'to'        => 'to@some-email.com',
            'subject'   => 'Some Subject',
            'body'      => 'Some body'
        ];

        $this->assertTrue(
            $this->mailService->send(
                $data['fromEmail'],
                $data['fromName'],
                $data['to'],
                $data['subject'],
                $data['body']
            )
        );

        $this->assertEquals(1, $transport->getLastMessage()->getFrom()->count());
        $this->assertEquals($data['fromEmail'], $transport->getLastMessage()->getFrom()->current()->getEmail());
        $this->assertEquals($data['fromName'], $transport->getLastMessage()->getFrom()->current()->getName());
        $this->assertEquals(1, $transport->getLastMessage()->getTo()->count());
        $this->assertEquals($data['subject'], $transport->getLastMessage()->getSubject());
        $this->assertEquals($data['body'], $transport->getLastMessage()->getBody());
    }

    public function testSendHtmlWithNullAdapter()
    {
        $transport = new NullTransport();

        $this->mailService->setMailTransport($transport);

        $data = [
            'fromEmail' => 'some@email.com',
            'fromName'  => 'Fred Smith',
            'to'        => 'to@some-email.com',
            'subject'   => 'Some Subject',
            'body'      => 'Some body',
            'html'      => '1',
        ];

        $this->assertTrue(
            $this->mailService->send(
                $data['fromEmail'],
                $data['fromName'],
                $data['to'],
                $data['subject'],
                $data['body'],
                $data['html']
            )
        );

        $this->assertEquals(1, $transport->getLastMessage()->getFrom()->count());
        $this->assertEquals(1, $transport->getLastMessage()->getTo()->count());
        $this->assertEquals($data['subject'], $transport->getLastMessage()->getSubject());

        $this->assertInstanceOf('\Zend\Mime\Message', $transport->getLastMessage()->getBody());
        $this->assertEquals($data['body'], $transport->getLastMessage()->getBody()->getPartContent(0));
    }

    public function testSendCorrectlyDealsWithException()
    {
        $transport = m::mock('Zend\Mail\Transport\Null');
        $transport->shouldReceive('send')->andThrow('Zend\Mail\Transport\Exception\RuntimeException', 'My message');

        $this->mailService->setMailTransport($transport);

        $data = [
            'fromEmail' => 'some@email.com',
            'fromName'  => 'Fred Smith',
            'to'        => 'to@some-email.com',
            'subject'   => 'Some Subject',
            'body'      => 'Some body'
        ];

        $this->assertEquals(
            'My message',
            $this->mailService->send(
                $data['fromEmail'],
                $data['fromName'],
                $data['to'],
                $data['subject'],
                $data['body']
            )
        );
    }

    public function testCreateServiceThrowsException()
    {
        $sl = new \Zend\ServiceManager\ServiceManager();
        $sl->setService('Config', []);

        $this->setExpectedException('Zend\Mail\Exception\RuntimeException', 'No mail config found');

        $this->mailService->createService($sl);
    }

    public function testCreateService()
    {
        $data = ['mail' => ['connection_class' => 'plain']];

        $sl = new \Zend\ServiceManager\ServiceManager();
        $sl->setService('Config', $data);

        $this->assertInstanceOf(
            'Zend\Mail\Transport\Sendmail',
            $this->mailService->createService($sl)->getMailTransport()
        );
    }
}
