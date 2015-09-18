<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Correspondence;

use Doctrine\ORM\Query;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\Correspondence\ProcessInboxDocuments as CommandHandler;
use Dvsa\Olcs\Api\Domain\Command\Correspondence\ProcessInboxDocuments as Command;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue as EnqueueFileCommand;
use Dvsa\Olcs\Api\Domain\Repository\CorrespondenceInbox as CorrespondenceInboxRepo;
use Mockery as m;
use Dvsa\Olcs\Email\Service\TemplateRenderer;
use Dvsa\Olcs\Email\Service\Client;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Email\Data\Message;

/**
 * Process inbox documents test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class ProcessInboxDocumentsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('CorrespondenceInbox', CorrespondenceInboxRepo::class);

        $this->mockedSmServices = [
            TemplateRenderer::class => m::mock(TemplateRenderer::class),
            Client::class => m::mock(Client::class),
        ];

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $command = Command::create([]);

        $mockOrganisation = m::mock()
            ->shouldReceive('getAdminEmailAddresses')
            ->andReturn(['foo@bar.com'])
            ->once()
            ->getMock();

        $mockLicence = m::mock()
            ->shouldReceive('getId')
            ->andReturn(1)
            ->twice()
            ->shouldReceive('getOrganisation')
            ->andReturn($mockOrganisation)
            ->once()
            ->shouldReceive('getLicNo')
            ->andReturn('licNo')
            ->once()
            ->getMock();

        $mockDocument = m::mock()
            ->shouldReceive('getContinuationDetails')
            ->andReturn('foo')
            ->once()
            ->shouldReceive('getIdentifier')
            ->andReturn('id')
            ->once()
            ->shouldReceive('getDescription')
            ->andReturn('desc')
            ->once()
            ->getMock();

        $mockInboxRecord = m::mock()
            ->shouldReceive('getLicence')
            ->andReturn($mockLicence)
            ->twice()
            ->shouldReceive('setEmailReminderSent')
            ->with('Y')
            ->once()
            ->shouldReceive('setPrinted')
            ->with('Y')
            ->once()
            ->shouldReceive('getDocument')
            ->andReturn($mockDocument)
            ->twice()
            ->getMock();

        $this->repoMap['CorrespondenceInbox']
            ->shouldReceive('getAllRequiringReminder')
            ->with(m::type(\DateTime::class), m::type(\DateTime::class))
            ->andReturn([$mockInboxRecord])
            ->once()
            ->shouldReceive('getAllRequiringPrint')
            ->with(m::type(\DateTime::class), m::type(\DateTime::class))
            ->andReturn([$mockInboxRecord])
            ->once()
            ->shouldReceive('save')
            ->with($mockInboxRecord)
            ->twice()
            ->getMock();

        $this->mockedSmServices[TemplateRenderer::class]->shouldReceive('renderBody')
            ->once()
            ->with(
                m::type(Message::class),
                'licensing-information-continuation',
                ['licNo' => 'licNo', 'url' => 'http://selfserve/correspondence'],
                null
            );

        $this->mockedSmServices[Client::class]->shouldReceive('sendEmail')
            ->once()
            ->with(m::type(Message::class))
            ->andReturnUsing(
                function (Message $message) {
                    $this->assertEquals(['foo@bar.com'], $message->getTo());
                }
            );

        $params = [
            'fileIdentifier' => 'id',
            'jobName' => 'desc'
        ];
        $this->expectedSideEffect(EnqueueFileCommand::class, $params, new Result());

        $expected = [
            'messages' => [
                'Found 1 records to email',
                'Sending email reminder for licence 1 to foo@bar.com',
                'Found 1 records to print',
                'Printing document for licence 1'
            ],
            'id' => []
        ];
        $result = $this->sut->handleCommand($command);
        $this->assertEquals($result->toArray(), $expected);
    }

    public function testHandleCommandNoUsers()
    {
        $command = Command::create([]);

        $mockOrganisation = m::mock()
            ->shouldReceive('getAdminEmailAddresses')
            ->andReturn([])
            ->once()
            ->getMock();

        $mockLicence = m::mock()
            ->shouldReceive('getId')
            ->andReturn(1)
            ->twice()
            ->shouldReceive('getOrganisation')
            ->andReturn($mockOrganisation)
            ->once()
            ->getMock();

        $mockDocument = m::mock()
            ->shouldReceive('getContinuationDetails')
            ->andReturn('foo')
            ->once()
            ->shouldReceive('getIdentifier')
            ->andReturn('id')
            ->once()
            ->shouldReceive('getDescription')
            ->andReturn('desc')
            ->once()
            ->getMock();

        $mockInboxRecord = m::mock()
            ->shouldReceive('getLicence')
            ->andReturn($mockLicence)
            ->twice()
            ->shouldReceive('setPrinted')
            ->with('Y')
            ->once()
            ->shouldReceive('getDocument')
            ->andReturn($mockDocument)
            ->twice()
            ->getMock();

        $this->repoMap['CorrespondenceInbox']
            ->shouldReceive('getAllRequiringReminder')
            ->with(m::type(\DateTime::class), m::type(\DateTime::class))
            ->andReturn([$mockInboxRecord])
            ->once()
            ->shouldReceive('getAllRequiringPrint')
            ->with(m::type(\DateTime::class), m::type(\DateTime::class))
            ->andReturn([$mockInboxRecord])
            ->once()
            ->shouldReceive('save')
            ->with($mockInboxRecord)
            ->once()
            ->getMock();

        $params = [
            'fileIdentifier' => 'id',
            'jobName' => 'desc'
        ];
        $this->expectedSideEffect(EnqueueFileCommand::class, $params, new Result());

        $expected = [
            'messages' => [
                'Found 1 records to email',
                'No admin email addresses for licence 1',
                'Found 1 records to print',
                'Printing document for licence 1'
            ],
            'id' => []
        ];
        $result = $this->sut->handleCommand($command);
        $this->assertEquals($result->toArray(), $expected);
    }
}
