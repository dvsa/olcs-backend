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
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mail\Transport\TransportInterface;
use Zend\Mime\Mime as ZendMime;
use Zend\Mime\Part as ZendMimePart;
use Zend\Mail\AddressList;
use Dvsa\Olcs\Email\Exception\EmailNotSentException;

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

    public function setUp(): void
    {
        $this->sut = new Email();
    }

    public function testCreateServiceMissingConfig()
    {
        $this->expectException(\Zend\Mail\Exception\RuntimeException::class);

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
                        'From: foo@bar.com',
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

        $this->sut->send(
            'foo@bar.com',
            'foo',
            'bar@foo.com',
            'Subject',
            'This is the content',
            null,
            ['cc@foo.com'],
            ['bcc@foo.com']
        );
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
                    //expecting 3 parts, multipart text/html followed by two attachments
                    $parts = $message->getBody()->getParts();
                    $this->assertCount(3, $parts);

                    /**
                     * @var ZendMimePart $messagePart
                     * @var ZendMimePart $attachmentPart
                     */
                    $messagePart = $parts[0];
                    $attachmentPart1 = $parts[1];
                    $attachmentPart2 = $parts[2];

                    $expectedPlainText = "Content-Type: " . ZendMime::TYPE_TEXT . "\n" .
                        "Content-Transfer-Encoding: " . ZendMime::ENCODING_QUOTEDPRINTABLE . "\n\n" .
                        "plain content";

                    $expectedHtml = "Content-Type: " . ZendMime::TYPE_HTML . "\n" .
                        "Content-Transfer-Encoding: " . ZendMime::ENCODING_QUOTEDPRINTABLE . "\n\n" .
                        "html content";

                    //test part one (this is a generated multipart message body, so we check both parts are included)
                    $this->assertStringContainsString($expectedPlainText, $messagePart->getRawContent());
                    $this->assertStringContainsString($expectedHtml, $messagePart->getRawContent());
                    $this->assertInstanceOf(ZendMimePart::class, $messagePart);

                    //test part two (the first attachment)
                    $this->assertEquals(ZendMime::TYPE_OCTETSTREAM, $attachmentPart1->type);
                    $this->assertEquals(ZendMime::ENCODING_BASE64, $attachmentPart1->encoding);
                    $this->assertEquals(ZendMime::DISPOSITION_ATTACHMENT, $attachmentPart1->disposition);
                    $this->assertEquals('docFilename', $attachmentPart1->filename);
                    $this->assertEquals('docContent', $attachmentPart1->getRawContent());
                    $this->assertInstanceOf(ZendMimePart::class, $attachmentPart1);

                    //test part three (the second attachment)
                    $this->assertEquals(ZendMime::TYPE_OCTETSTREAM, $attachmentPart2->type);
                    $this->assertEquals(ZendMime::ENCODING_BASE64, $attachmentPart2->encoding);
                    $this->assertEquals(ZendMime::DISPOSITION_ATTACHMENT, $attachmentPart2->disposition);
                    $this->assertEquals('docFilename2', $attachmentPart2->filename);
                    $this->assertEquals('docContent2', $attachmentPart2->getRawContent());
                    $this->assertInstanceOf(ZendMimePart::class, $attachmentPart2);

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
            ],
            1 => [
                'content' => 'docContent2',
                'fileName' => 'docFilename2'
            ]
        ];

        $cc = ['invalid-email', 'cc1@foo.com', 'cc2@foo.com'];
        $bcc = [null, 'bcc1@foo.com', 'bcc2@foo.com', 'bcc3@foo.com'];

        $this->sut->send(
            'foo@bar.com',
            'foo',
            'bar@foo.com',
            'msg subject',
            'plain content',
            'html content',
            $cc,
            $bcc,
            $docs
        );
    }

    /**
     * Tests sending an email without attachments
     */
    public function testSendWithoutAttachments()
    {
        $transport = m::mock(TransportInterface::class);

        $this->sut->setMailTransport($transport);

        $transport->shouldReceive('send')
            ->once()
            ->with(m::type(Message::class))
            ->andReturnUsing(
                function (Message $message) {
                    //expecting 2 parts, text and html
                    $parts = $message->getBody()->getParts();
                    $this->assertCount(2, $parts);

                    /**
                     * @var ZendMimePart $plainPart
                     * @var ZendMimePart $htmlPart
                     */
                    $plainPart = $parts[0];
                    $htmlPart = $parts[1];

                    //test part one (plain text)
                    $this->assertEquals('plain content', $plainPart->getRawContent());
                    $this->assertEquals(ZendMime::TYPE_TEXT, $plainPart->type);
                    $this->assertEquals(ZendMime::ENCODING_QUOTEDPRINTABLE, $plainPart->encoding);
                    $this->assertInstanceOf(ZendMimePart::class, $plainPart);

                    //test part two (html)
                    $this->assertEquals('html content', $htmlPart->getRawContent());
                    $this->assertEquals(ZendMime::TYPE_HTML, $htmlPart->type);
                    $this->assertEquals(ZendMime::ENCODING_QUOTEDPRINTABLE, $htmlPart->encoding);
                    $this->assertInstanceOf(ZendMimePart::class, $htmlPart);

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
                    $this->assertEquals(ZendMime::MULTIPART_ALTERNATIVE, $headers->get('content-type')->getType());
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

        $cc = ['cc1@foo.com', 'cc2@foo.com', 'invalid-email'];
        $bcc = ['bcc1@foo.com', 'bcc2@foo.com', 'bcc3@foo.com', null];

        $this->sut->send(
            'foo@bar.com',
            'foo',
            'bar@foo.com',
            'msg subject',
            'plain content',
            'html content',
            $cc,
            $bcc,
            []
        );
    }

    /**
     * Tests sending an email without attachments
     *
     * @dataProvider toFromAddressProvider
     */
    public function testToFromAddressException($fromEmail, $fromName, $toEmail, $exceptionMessage)
    {
        $this->expectException(EmailNotSentException::class);
        $this->expectExceptionMessage($exceptionMessage);

        $this->sut->send(
            $fromEmail,
            $fromName,
            $toEmail,
            'msg subject',
            'plain content',
            'html content',
            [],
            [],
            []
        );
    }

    /**
     * @return array
     */
    public function toFromAddressProvider()
    {
        return [
            ['foo@bar.com', 'from name', null, Email::MISSING_TO_ERROR],
            ['foo@bar.com', null, null, Email::MISSING_TO_ERROR],
            [null, 'from name', 'foo@bar.com', Email::MISSING_FROM_ERROR],
            [null, null, 'foo@bar.com', Email::MISSING_FROM_ERROR],
        ];
    }

    public function testSendHandlesException()
    {
        $this->expectException(\Dvsa\Olcs\Email\Exception\EmailNotSentException::class);
        $this->expectExceptionMessage('Email not sent: exception message');

        $transport = m::mock(TransportInterface::class);

        $this->sut->setMailTransport($transport);

        $transport->shouldReceive('send')
            ->once()
            ->with(m::type(Message::class))
            ->andThrow(new \Exception('exception message'));

        $this->sut->send(
            'foo@bar.com',
            'foo',
            'bar@foo.com',
            'Subject',
            'This is the content',
            null
        );
    }
}
