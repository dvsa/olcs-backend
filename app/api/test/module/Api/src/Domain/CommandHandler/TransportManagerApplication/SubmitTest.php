<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\TransportManagerApplication;

use Dvsa\Olcs\Api\Domain\CommandHandler\TransportManagerApplication\Submit as CommandHandler;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;
use Dvsa\Olcs\Transfer\Command\TransportManagerApplication\UpdateStatus as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Email\Service\Client;
use Dvsa\Olcs\Email\Service\TemplateRenderer;

/**
 * SubmitTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class SubmitTest extends CommandHandlerTestCase
{
    protected $loggedInUser;

    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo(
            'TransportManagerApplication',
            \Dvsa\Olcs\Api\Domain\Repository\TransportManagerApplication::class
        );

        $this->mockedSmServices = [
            Client::class => m::mock(Client::class),
            TemplateRenderer::class => m::mock(TemplateRenderer::class),
        ];

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            TransportManagerApplication::STATUS_TM_SIGNED,
            TransportManagerApplication::STATUS_OPERATOR_SIGNED,
        ];

        parent::initReferences();
    }

    public function testHandleCommandWithVersion()
    {
        $command = Command::create(['id' => 863, 'version' => 234]);

        $tma = m::mock(TransportManagerApplication::class)->makePartial();
        $tma->setIsOwner('N');
        $tma->shouldReceive('getApplication->getLicence->getOrganisation->getAdminEmailAddresses')->with()->once()
            ->andReturn([]);

        $this->repoMap['TransportManagerApplication']->shouldReceive('fetchUsingId')->once()
            ->with($command, \Doctrine\ORM\Query::HYDRATE_OBJECT, 234)->andReturn($tma);
        $this->repoMap['TransportManagerApplication']->shouldReceive('save')->once()->andReturnUsing(
            function (TransportManagerApplication $tma) {
                $this->assertSame(
                    $this->refData[TransportManagerApplication::STATUS_TM_SIGNED],
                    $tma->getTmApplicationStatus()
                );
            }
        );

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandIsOwnerY()
    {
        $command = Command::create(['id' => 863]);

        $tma = new TransportManagerApplication();
        $tma->setIsOwner('Y');

        $this->repoMap['TransportManagerApplication']->shouldReceive('fetchUsingId')->once()
            ->with($command)->andReturn($tma);
        $this->repoMap['TransportManagerApplication']->shouldReceive('save')->once()->andReturnUsing(
            function (TransportManagerApplication $tma) {
                $this->assertSame(
                    $this->refData[TransportManagerApplication::STATUS_OPERATOR_SIGNED],
                    $tma->getTmApplicationStatus()
                );
            }
        );

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandSendEmailToAdminsRemoveDuplicate()
    {
        $command = Command::create(['id' => 863]);

        $creatorContactDetails = m::mock();
        $creatorContactDetails->shouldReceive('getContactDetails->getEmailAddress')->with()->once()
            ->andReturn('email1');

        $tma = m::mock(TransportManagerApplication::class)->makePartial();
        $tma->setId(12);
        $tma->shouldReceive('getCreatedBy')->with()->twice()->andReturn($creatorContactDetails);
        $tma->shouldReceive('getApplication->getLicence->getOrganisation->getAdminEmailAddresses')->with()->once()
            ->andReturn(['email1']);

        $tma->shouldReceive('getApplication->getNiFlag')->with()->once()->andReturn('Y');
        $tma->shouldReceive('getTransportManager->getHomeCd->getPerson->getFullName')->with()->once()
            ->andReturn('Bob Smith');
        $tma->shouldReceive('getApplication->getLicence->getLicNo')->with()->once()->andReturn('LIC01');
        $tma->shouldReceive('getApplication->getId')->with()->twice()->andReturn(76);

        $this->repoMap['TransportManagerApplication']->shouldReceive('fetchUsingId')->once()
            ->with($command)->andReturn($tma);
        $this->repoMap['TransportManagerApplication']->shouldReceive('save')->once();

        $this->mockedSmServices[TemplateRenderer::class]->shouldReceive('renderBody')->once()->andReturnUsing(
            function (\Dvsa\Olcs\Email\Data\Message $message, $template, $vars, $layout) {

                $this->assertSame('email.transport-manager-submitted-form.subject', $message->getSubject());
                $this->assertSame('email1', $message->getTo());
                $this->assertSame('cy_GB', $message->getLocale());

                $this->assertSame('transport-manager-submitted-form', $template);
                $this->assertSame(
                    [
                        'tmFullName' => 'Bob Smith',
                        'licNo' => 'LIC01',
                        'applicationId' => 76,
                        'tmaUrl' => 'http://selfserve/application/76/transport-managers/details/12/'
                    ],
                    $vars
                );
                $this->assertNull($layout);

            }
        );
        $this->mockedSmServices[Client::class]->shouldReceive('sendEmail')
            ->with(m::type(\Dvsa\Olcs\Email\Data\Message::class))->once();

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandSendEmailToCreator()
    {
        $command = Command::create(['id' => 863]);

        $creatorContactDetails = m::mock();
        $creatorContactDetails->shouldReceive('getContactDetails->getEmailAddress')->with()->once()
            ->andReturn('email1');

        $tma = m::mock(TransportManagerApplication::class)->makePartial();
        $tma->setId(12);
        $tma->shouldReceive('getCreatedBy')->with()->twice()->andReturn($creatorContactDetails);
        $tma->shouldReceive('getApplication->getLicence->getOrganisation->getAdminEmailAddresses')->with()->once()
            ->andReturn([]);

        $tma->shouldReceive('getApplication->getNiFlag')->with()->once()->andReturn('Y');
        $tma->shouldReceive('getTransportManager->getHomeCd->getPerson->getFullName')->with()->once()
            ->andReturn('Bob Smith');
        $tma->shouldReceive('getApplication->getLicence->getLicNo')->with()->once()->andReturn('LIC01');
        $tma->shouldReceive('getApplication->getId')->with()->twice()->andReturn(76);

        $this->repoMap['TransportManagerApplication']->shouldReceive('fetchUsingId')->once()
            ->with($command)->andReturn($tma);
        $this->repoMap['TransportManagerApplication']->shouldReceive('save')->once();

        $this->mockedSmServices[TemplateRenderer::class]->shouldReceive('renderBody')->once()->andReturnUsing(
            function (\Dvsa\Olcs\Email\Data\Message $message, $template, $vars, $layout) {

                $this->assertSame('email.transport-manager-submitted-form.subject', $message->getSubject());
                $this->assertSame('email1', $message->getTo());
                $this->assertSame('cy_GB', $message->getLocale());

                $this->assertSame('transport-manager-submitted-form', $template);
                $this->assertSame(
                    [
                        'tmFullName' => 'Bob Smith',
                        'licNo' => 'LIC01',
                        'applicationId' => 76,
                        'tmaUrl' => 'http://selfserve/application/76/transport-managers/details/12/'
                    ],
                    $vars
                );
                $this->assertNull($layout);

            }
        );
        $this->mockedSmServices[Client::class]->shouldReceive('sendEmail')
            ->with(m::type(\Dvsa\Olcs\Email\Data\Message::class))->once();

        $this->sut->handleCommand($command);
    }
}
