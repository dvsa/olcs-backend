<?php

/**
 * Send Email Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Email\Domain\CommandHandler;

use Dvsa\Olcs\Email\Service\Email;
use Mockery as m;
use Dvsa\Olcs\Email\Domain\CommandHandler\SendEmail;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Zend\I18n\Translator\Translator;
use Dvsa\Olcs\Email\Domain\Command\SendEmail as Cmd;
use Dvsa\Olcs\Api\Service\File\ContentStoreFileUploader;
use Dvsa\Olcs\DocumentShare\Data\Object\File;
use Dvsa\Olcs\Api\Entity\Doc\Document as DocumentEntity;
use Dvsa\Olcs\Api\Domain\Repository\Document as DocumentRepo;

/**
 * Send Email Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class SendEmailTest extends CommandHandlerTestCase
{
    /**
     * @var SendEmail
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new SendEmail();
        $this->mockRepo('Document', DocumentRepo::class);

        $this->mockedSmServices['Config'] = [
            'email' => [
                'from_name' => 'Terry',
                'from_email' => 'terry.valtech@gmail.com',
                'send_all_mail_to' => 'terry.valtech@gmail.com',
                'selfserve_uri' => 'http://olcs-selfserve/',
                'internal_uri' => 'http://olcs-internal/'
            ]
        ];

        $this->mockedSmServices['FileUploader'] = m::mock(ContentStoreFileUploader::class);

        $this->mockedSmServices['translator'] = m::mock(Translator::class);
        $this->mockedSmServices['translator']->shouldReceive('translate')->andReturnUsing(
            function ($message) {
                return 'translated-' . $message;
            }
        );

        $this->mockedSmServices['EmailService'] = m::mock(Email::class);

        parent::setUp();
    }

    public function testHandleCommandEmptyBody()
    {
        $this->setExpectedException(\RuntimeException::class);

        $data = [
            'body' => ''
        ];

        $command = Cmd::create($data);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommand()
    {
        $data = [
            'to' => 'foo@bar.com',
            'cc' => ['bar@foo.com'],
            'bcc' => ['bcc@foobar.com'],
            'docs' => [],
            'subject' => 'Some subject',
            'html' => false,
            'body' => 'This is the email'
        ];

        $command = Cmd::create($data);

        $this->repoMap['Document']
            ->shouldReceive('fetchByIds')
            ->once()
            ->with([])
            ->andReturn([]);

        $this->mockedSmServices['EmailService']->shouldReceive('send')
            ->once()
            ->with(
                'terry.valtech@gmail.com',
                'Terry',
                'terry.valtech@gmail.com',
                'foo@bar.com : translated-Some subject',
                'translated-This is the email',
                false,
                [],
                [],
                []
            );

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandAlt()
    {
        $docId1 = 33;
        $docIdentifier1 = 'abcde123';
        $docFileName1 = 'filename1';
        $docPath1 = '/path/to/' . $docFileName1;
        $docContent1 = 'content1';

        $docId2 = 66;
        $docIdentifier2 = 'fghij456';
        $docFileName2 = 'filename2';
        $docPath2 = '/path/to/' . $docFileName2;
        $docContent2 = 'content2';

        $docIds = [$docId1, $docId2];

        $data = [
            'fromName' => 'Foo',
            'fromEmail' => 'foobar@cake.com',
            'to' => 'foo@bar.com',
            'cc' => ['bar@foo.com'],
            'bcc' => ['bcc@foobar.com'],
            'docs' => $docIds,
            'subject' => 'Some subject',
            'html' => false,
            'body' => 'This is the email http://selfserve/ http://internal/'
        ];

        $command = Cmd::create($data);

        $this->sut->setSendAllMailTo(null);

        $document1 = m::mock(DocumentEntity::class);
        $document1->shouldReceive('getIdentifier')->once()->andReturn($docIdentifier1);
        $document1->shouldReceive('getFilename')->once()->andReturn($docPath1);
        $document2 = m::mock(DocumentEntity::class);
        $document2->shouldReceive('getIdentifier')->once()->andReturn($docIdentifier2);
        $document2->shouldReceive('getFilename')->once()->andReturn($docPath2);

        $docFile1 = m::mock(File::class);
        $docFile1->shouldReceive('getContent')->once()->andReturn($docContent1);

        $docFile2 = m::mock(File::class);
        $docFile2->shouldReceive('getContent')->once()->andReturn($docContent2);

        $this->repoMap['Document']
            ->shouldReceive('fetchByIds')
            ->once()
            ->with($docIds)
            ->andReturn([$document1, $document2]);

        $this->mockedSmServices['FileUploader']
            ->shouldReceive('download')
            ->once()
            ->with($docIdentifier1)
            ->andReturn($docFile1);

        $this->mockedSmServices['FileUploader']
            ->shouldReceive('download')
            ->once()
            ->with($docIdentifier2)
            ->andReturn($docFile2);

        $expectedDocs = [
            0 => [
                'fileName' => $docFileName1,
                'content' => $docContent1
            ],
            1 => [
                'fileName' => $docFileName2,
                'content' => $docContent2
            ]
        ];

        $this->mockedSmServices['EmailService']->shouldReceive('send')
            ->once()
            ->with(
                'foobar@cake.com',
                'Foo',
                'foo@bar.com',
                'translated-Some subject',
                'translated-This is the email http://olcs-selfserve/ http://olcs-internal/',
                false,
                ['bar@foo.com'],
                ['bcc@foobar.com'],
                $expectedDocs
            );

        $this->sut->handleCommand($command);
    }
}
