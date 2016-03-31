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
use Zend\Mime\Mime as ZendMime;
use Zend\Mime\Part as ZendMimePart;
use Zend\Mail\AddressList;

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

    /**
     * Tests create service
     */
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

    /**
     * Tests sending html email
     */
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
                        'Cc: cc@foo.com',
                        'Bcc: bcc@foo.com',
                        'Subject: Subject',
                        'MIME-Version: 1.0',
                        'Content-Type: ' . ZendMime::TYPE_HTML,
                        'Content-Transfer-Encoding: ' . ZendMime::ENCODING_QUOTEDPRINTABLE,
                        '',
                        'This is the content'
                    ];

                    $this->assertEquals($expected, $parts);
                }
            );

        $this->sut->send('foo@bar.com', 'foo', 'bar@foo.com', 'Subject', 'This is the content', true, ['cc@foo.com'], ['bcc@foo.com'], []);
    }

    /**
     * Tests sending plain text email
     */
    public function testSendText()
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
                        'Cc: cc@foo.com',
                        'Bcc: bcc@foo.com',
                        'Subject: Subject',
                        'MIME-Version: 1.0',
                        'Content-Type: ' . ZendMime::TYPE_TEXT,
                        'Content-Transfer-Encoding: ' . ZendMime::ENCODING_QUOTEDPRINTABLE,
                        '',
                        'This is the content'
                    ];

                    $this->assertEquals($expected, $parts);
                }
            );

        $this->sut->send('foo@bar.com', 'foo', 'bar@foo.com', 'Subject', 'This is the content', false, ['cc@foo.com'], ['bcc@foo.com']);
    }

    /**
     * Tests sending an email with attachments
     */
    public function testSendWithAttachments()
    {
        $transport = m::mock(TransportInterface::class);

        $this->sut->setMailTransport($transport);

        $transport->shouldReceive('send')
            ->once()
            ->with(m::type(Message::class))
            ->andReturnUsing(
                function (Message $message) {
                    $parts = $message->getBody()->getParts();
                    $this->assertCount(2, $parts);

                    /**
                     * @var ZendMimePart $messagePart
                     * @var ZendMimePart $attachmentPart
                     */
                    $messagePart = $parts[0];
                    $attachmentPart = $parts[1];

                    //test part one (the message text)
                    $this->assertEquals(ZendMime::TYPE_HTML, $messagePart->type);
                    $this->assertEquals(ZendMime::ENCODING_QUOTEDPRINTABLE, $messagePart->encoding);
                    $this->assertEquals('msg content', $messagePart->getRawContent());
                    $this->assertInstanceOf(ZendMimePart::class, $messagePart);

                    //test part two (the attachment)
                    $this->assertEquals(ZendMime::TYPE_OCTETSTREAM, $attachmentPart->type);
                    $this->assertEquals(ZendMime::ENCODING_BASE64, $attachmentPart->encoding);
                    $this->assertEquals(ZendMime::DISPOSITION_ATTACHMENT, $attachmentPart->disposition);
                    $this->assertEquals('docFilename', $attachmentPart->filename);
                    $this->assertEquals('docContent', $attachmentPart->getRawContent());
                    $this->assertInstanceOf(ZendMimePart::class, $attachmentPart);

                    /**
                     * @var AddressList $from
                     * @var AddressList $toList
                     * @var AddressList $ccList
                     * @var AddressList $bccList
                     */
                    $headers = $message->getHeaders();
                    $from = $headers->get('from')->getAddressList();
                    $toList = $headers->get('to')->getAddressList();
                    $ccList = $headers->get('cc')->getAddressList();
                    $bccList = $headers->get('bcc')->getAddressList();

                    //test mail headers
                    $this->assertEquals(ZendMime::MULTIPART_MIXED, $headers->get('content-type')->getType());
                    $this->assertEquals('msg subject', $headers->get('subject')->getFieldValue());
                    $this->assertEquals(true, $from->has('foo@bar.com'));
                    $this->assertEquals(1, $from->count());
                    $this->assertEquals(true, $toList->has('bar@foo.com'));
                    $this->assertEquals(1, $toList->count());
                    $this->assertEquals(true, $ccList->has('cc1@foo.com'));
                    $this->assertEquals(true, $ccList->has('cc2@foo.com'));
                    $this->assertEquals(2, $ccList->count());
                    $this->assertEquals(true, $bccList->has('bcc1@foo.com'));
                    $this->assertEquals(true, $bccList->has('bcc2@foo.com'));
                    $this->assertEquals(true, $bccList->has('bcc3@foo.com'));
                    $this->assertEquals(3, $bccList->count());
                }
            );

        $docs = [
            0 => [
                'content' => 'docContent',
                'fileName' => 'docFilename'
            ]
        ];

        $cc = ['cc1@foo.com', 'cc2@foo.com'];
        $bcc = ['bcc1@foo.com', 'bcc2@foo.com', 'bcc3@foo.com'];

        $this->sut->send('foo@bar.com', 'foo', 'bar@foo.com', 'msg subject', 'msg content', true, $cc, $bcc, $docs);
    }
}
