<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\TransportManagerApplication;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\TransportManagerApplication\Snapshot;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransportManagerApplication\Submit as CommandHandler;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationUser;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager;
use Dvsa\Olcs\Email\Domain\Command\SendEmail;
use Dvsa\Olcs\Transfer\Command\TransportManagerApplication\Submit as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
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
        $this->mockRepo(
            'TransportManager',
            \Dvsa\Olcs\Api\Domain\Repository\TransportManager::class
        );

        $this->mockedSmServices = [
            TemplateRenderer::class => m::mock(TemplateRenderer::class),
        ];

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            TransportManagerApplication::STATUS_TM_SIGNED,
            TransportManagerApplication::STATUS_OPERATOR_SIGNED,
            TransportManagerApplication::TYPE_EXTERNAL,
            TransportManagerApplication::STATUS_RECEIVED,
        ];

        parent::initReferences();
    }

    public function testHandleCommandWithVersion()
    {
        $command = Command::create(['id' => 863, 'version' => 234]);

        $organisation = new Organisation();

        $tma = m::mock(TransportManagerApplication::class)->makePartial();

        $tma->setIsOwner('N');
        $tma->shouldReceive('getApplication->getLicence->getOrganisation')->with()->once()
            ->andReturn($organisation);

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

    public function testHandleCommandWithNextStatus()
    {
        $command = Command::create(['id' => 863, 'nextStatus' => TransportManagerApplication::STATUS_RECEIVED]);

        /** @var TransportManagerApplication $tma */
        $tma = m::mock(TransportManagerApplication::class)->makePartial();

        $tma->setIsOwner('N');
        $tma->setId(863);

        $tma->shouldReceive('getTransportManager->getTmType')
            ->with()
            ->once()
            ->andReturn($this->refData[TransportManagerApplication::TYPE_EXTERNAL]);
        $tma->shouldReceive('getTransportManager->getId')
            ->with()
            ->once()
            ->andReturn(111);

        $this->repoMap['TransportManagerApplication']
            ->shouldReceive('fetchUsingId')
            ->once()
            ->with($command)
            ->andReturn($tma);

        $this->repoMap['TransportManagerApplication']
            ->shouldReceive('save')
            ->once()
            ->andReturnUsing(
                function (TransportManagerApplication $tma) {
                    $this->assertSame(
                        $this->refData[TransportManagerApplication::STATUS_RECEIVED],
                        $tma->getTmApplicationStatus()
                    );
                }
            );

        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\TransportManagerApplication\Snapshot::class,
            ['id' => 863],
            new Result()
        );


        $this->sut->handleCommand($command);
    }

    public function testHandleCommandIsOwnerY()
    {
        $command = Command::create(['id' => 863]);

        $tma = new TransportManagerApplication();
        $tma->setIsOwner('Y');
        $tma->setId(1);
        $tma->setTmType(TransportManagerApplication::TYPE_EXTERNAL);
        $tma->setTransportManager(
            m::mock(TransportManager::class)->makePartial()->shouldReceive('getId')->once()->andReturn(1)->getMock()
        );

        $data = [
            'id' => 1,
            'user' => 1
        ];

        $result = new Result();
        $this->expectedSideEffect(Snapshot::class, $data, $result);

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
        $this->repoMap['TransportManager']->shouldReceive('save')->once()->andReturnUsing(
            function (TransportManager $tm) {
                $this->assertSame(
                    $this->refData[TransportManagerApplication::TYPE_EXTERNAL],
                    $tm->getTmType()
                );
            }
        );

        $this->sut->handleCommand($command);
    }

    public function dataProviderTestHandleCommand()
    {
        return [
            [false, 'application'],
            [true, 'variation']
        ];
    }
    /**
     * @dataProvider dataProviderTestHandleCommand
     */
    public function testHandleCommandSendEmailToAdminsRemoveDuplicate($isVariation, $uriSegment)
    {
        $command = Command::create(['id' => 863]);

        $creator = m::mock(User::class)->makePartial();
        $creator->setId(1);
        $creator->setTranslateToWelsh('N');
        $creator->shouldReceive('getContactDetails->getEmailAddress')->with()->once()
            ->andReturn('email1');

        $user = m::mock(User::class)->makePartial();
        $user->setId(2);
        $user->setTranslateToWelsh('Y');
        $user->shouldReceive('getContactDetails->getEmailAddress')->with()->once()
            ->andReturn('foo@bar.com');

        $orgUser = new OrganisationUser();
        $orgUser->setUser($user);
        $orgUser->setIsAdministrator('Y');

        $orgUserCreator = new OrganisationUser();
        $orgUserCreator->setUser($creator);
        $orgUserCreator->setIsAdministrator('Y');

        $organisation = new Organisation();
        $organisation->addOrganisationUsers($orgUser);
        $organisation->addOrganisationUsers($orgUserCreator);

        $tma = m::mock(TransportManagerApplication::class)->makePartial();
        $tma->setId(12);
        $tma->shouldReceive('getCreatedBy')->with()->andReturn($creator);
        $tma->shouldReceive('getApplication->getLicence->getOrganisation')->with()->once()
            ->andReturn($organisation);

        $tma->shouldReceive('getTransportManager->getHomeCd->getPerson->getFullName')->with()->once()
            ->andReturn('Bob Smith');
        $tma->shouldReceive('getApplication->getLicence->getLicNo')->with()->once()->andReturn('LIC01');
        $tma->shouldReceive('getApplication->getId')->with()->twice()->andReturn(76);
        $tma->shouldReceive('getApplication->getIsVariation')->with()->once()->andReturn($isVariation);

        $this->repoMap['TransportManagerApplication']->shouldReceive('fetchUsingId')->once()
            ->with($command)->andReturn($tma);
        $this->repoMap['TransportManagerApplication']->shouldReceive('save')->once();

        $this->mockedSmServices[TemplateRenderer::class]->shouldReceive('renderBody')->times(2)->andReturnUsing(
            function (\Dvsa\Olcs\Email\Data\Message $message, $template, $vars, $layout) use ($uriSegment) {

                $this->assertSame('email.transport-manager-submitted-form.subject', $message->getSubject());
                $this->assertSame('foo@bar.com', $message->getTo());
                $this->assertSame('cy_GB', $message->getLocale());
                $this->assertSame('transport-manager-submitted-form', $template);
                $this->assertSame(
                    [
                        'tmFullName' => 'Bob Smith',
                        'licNo' => 'LIC01',
                        'applicationId' => 76,
                        'tmaUrl' => 'http://selfserve/'.$uriSegment.'/76/transport-managers/details/12/'
                    ],
                    $vars
                );
                $this->assertSame('default', $layout);
            },
            function (\Dvsa\Olcs\Email\Data\Message $message, $template, $vars, $layout) use ($uriSegment) {

                $this->assertSame('email.transport-manager-submitted-form.subject', $message->getSubject());
                $this->assertSame('email1', $message->getTo());
                $this->assertSame('en_GB', $message->getLocale());
                $this->assertSame('transport-manager-submitted-form', $template);
                $this->assertSame(
                    [
                        'tmFullName' => 'Bob Smith',
                        'licNo' => 'LIC01',
                        'applicationId' => 76,
                        'tmaUrl' => 'http://selfserve/'.$uriSegment.'/76/transport-managers/details/12/'
                    ],
                    $vars
                );
                $this->assertSame('default', $layout);
            }
        );

        $result = new Result();
        $this->expectedSideEffect(
            SendEmail::class,
            [
                'to' => 'foo@bar.com'
            ],
            $result
        );
        $this->expectedSideEffect(
            SendEmail::class,
            [
                'to' => 'email1'
            ],
            $result
        );

        $this->sut->handleCommand($command);
    }

    /**
     * @dataProvider dataProviderTestHandleCommand
     */
    public function testHandleCommandSendEmailToCreator($isVariation, $uriSegment)
    {
        $command = Command::create(['id' => 863]);

        $creator = m::mock(User::class)->makePartial();
        $creator->setId(1);
        $creator->setTranslateToWelsh('N');
        $creator->shouldReceive('getContactDetails->getEmailAddress')->with()->once()
            ->andReturn('email1');

        $user = m::mock(User::class)->makePartial();
        $user->setId(2);
        $user->setTranslateToWelsh('Y');
        $user->shouldReceive('getContactDetails->getEmailAddress')->with()->once()
            ->andReturn('foo@bar.com');

        $orgUser = new OrganisationUser();
        $orgUser->setUser($user);
        $orgUser->setIsAdministrator('Y');

        $organisation = new Organisation();
        $organisation->addOrganisationUsers($orgUser);

        $tma = m::mock(TransportManagerApplication::class)->makePartial();
        $tma->setId(12);
        $tma->shouldReceive('getCreatedBy')->with()->andReturn($creator);
        $tma->shouldReceive('getApplication->getLicence->getOrganisation')->with()->once()
            ->andReturn($organisation);

        $tma->shouldReceive('getTransportManager->getHomeCd->getPerson->getFullName')->with()->once()
            ->andReturn('Bob Smith');
        $tma->shouldReceive('getApplication->getLicence->getLicNo')->with()->once()->andReturn('LIC01');
        $tma->shouldReceive('getApplication->getId')->with()->twice()->andReturn(76);
        $tma->shouldReceive('getApplication->getIsVariation')->with()->once()->andReturn($isVariation);

        $this->repoMap['TransportManagerApplication']->shouldReceive('fetchUsingId')->once()
            ->with($command)->andReturn($tma);
        $this->repoMap['TransportManagerApplication']->shouldReceive('save')->once();

        $this->mockedSmServices[TemplateRenderer::class]->shouldReceive('renderBody')->times(2)->andReturnUsing(
            function (\Dvsa\Olcs\Email\Data\Message $message, $template, $vars, $layout) use ($uriSegment) {

                $this->assertSame('email.transport-manager-submitted-form.subject', $message->getSubject());
                $this->assertSame('foo@bar.com', $message->getTo());
                $this->assertSame('cy_GB', $message->getLocale());
                $this->assertSame('transport-manager-submitted-form', $template);
                $this->assertSame(
                    [
                        'tmFullName' => 'Bob Smith',
                        'licNo' => 'LIC01',
                        'applicationId' => 76,
                        'tmaUrl' => 'http://selfserve/'.$uriSegment.'/76/transport-managers/details/12/'
                    ],
                    $vars
                );
                $this->assertSame('default', $layout);
            },
            function (\Dvsa\Olcs\Email\Data\Message $message, $template, $vars, $layout) use ($uriSegment) {

                $this->assertSame('email.transport-manager-submitted-form.subject', $message->getSubject());
                $this->assertSame('email1', $message->getTo());
                $this->assertSame('en_GB', $message->getLocale());
                $this->assertSame('transport-manager-submitted-form', $template);
                $this->assertSame(
                    [
                        'tmFullName' => 'Bob Smith',
                        'licNo' => 'LIC01',
                        'applicationId' => 76,
                        'tmaUrl' => 'http://selfserve/'.$uriSegment.'/76/transport-managers/details/12/'
                    ],
                    $vars
                );
                $this->assertSame('default', $layout);
            }
        );

        $result = new Result();
        $this->expectedSideEffect(
            SendEmail::class,
            [
                'to' => 'foo@bar.com'
            ],
            $result
        );
        $this->expectedSideEffect(
            SendEmail::class,
            [
                'to' => 'email1'
            ],
            $result
        );

        $this->sut->handleCommand($command);
    }
}
