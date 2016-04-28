<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Correspondence;

use Dvsa\Olcs\Email\Domain\Command\SendEmail;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\Correspondence\ProcessInboxDocuments as CommandHandler;
use Dvsa\Olcs\Api\Domain\Command\Correspondence\ProcessInboxDocuments as Command;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue as EnqueueFileCommand;
use Dvsa\Olcs\Api\Domain\Repository\CorrespondenceInbox as CorrespondenceInboxRepo;
use Mockery as m;
use Dvsa\Olcs\Email\Service\TemplateRenderer;
use Dvsa\Olcs\Email\Data\Message;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationUser;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Api\Entity\System\RefData;

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
            TemplateRenderer::class => m::mock(TemplateRenderer::class)
        ];

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $command = Command::create([]);

        $cd = new ContactDetails(m::mock(RefData::class));
        $cd->setEmailAddress('foo@bar.com');

        $user = new User('pid', 'TYPE');
        $user->setContactDetails($cd);
        $user->setTranslateToWelsh('Y');

        $orgUser = new OrganisationUser();
        $orgUser->setUser($user);
        $orgUser->setIsAdministrator('Y');

        $organisation = new Organisation();
        $organisation->addOrganisationUsers($orgUser);

        $mockLicence = m::mock()
            ->shouldReceive('getId')
            ->andReturn(1)
            ->twice()
            ->shouldReceive('getOrganisation')
            ->andReturn($organisation)
            ->shouldReceive('getLicNo')
            ->andReturn('licNo')
            ->once()
            ->getMock();

        $mockDocument = m::mock()
            ->shouldReceive('getContinuationDetails')
            ->andReturn(
                [
                    m::mock()
                    ->shouldReceive('getChecklistDocument')
                    ->andReturn('foo')
                    ->once()
                    ->getMock()
                ]
            )
            ->twice()
            ->shouldReceive('getId')
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
            ->times(3)
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
                'email-inbox-reminder-continuation',
                ['licNo' => 'licNo', 'url' => 'http://selfserve/correspondence'],
                'default'
            );

        $result = new Result();
        $data = [
            'to' => 'foo@bar.com'
        ];

        $this->expectedSideEffect(SendEmail::class, $data, $result);

        $params = [
            'documentId' => 'id',
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

        $organisation = new Organisation();

        $mockLicence = m::mock()
            ->shouldReceive('getId')
            ->andReturn(1)
            ->twice()
            ->shouldReceive('getOrganisation')
            ->andReturn($organisation)
            ->once()
            ->getMock();

        $mockDocument = m::mock()
            ->shouldReceive('getContinuationDetails')
            ->andReturn(
                [
                    m::mock()
                        ->shouldReceive('getChecklistDocument')
                        ->andReturn('foo')
                        ->once()
                        ->getMock()
                ]
            )
            ->twice()
            ->shouldReceive('getId')
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
            ->times(3)
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
            'documentId' => 'id',
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
