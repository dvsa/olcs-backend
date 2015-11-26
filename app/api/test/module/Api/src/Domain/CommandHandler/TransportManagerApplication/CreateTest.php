<?php

/**
 * CreateTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\TransportManagerApplication;

use Dvsa\Olcs\Api\Domain\CommandHandler\TransportManagerApplication\Create as CommandHandler;
use Dvsa\Olcs\Api\Domain\Repository\TransportManager;
use Dvsa\Olcs\Api\Domain\Repository\TransportManagerApplication;
use Dvsa\Olcs\Api\Domain\Repository\User;
use Dvsa\Olcs\Transfer\Command\TransportManagerApplication\Create as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication as TransportManagerApplicationEntity;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;

/**
 * CreateTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class CreateTest extends CommandHandlerTestCase
{
    protected $loggedInUser;

    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('TransportManagerApplication', TransportManagerApplication::class);
        $this->mockRepo('TransportManager', TransportManager::class);
        $this->mockRepo('User', User::class);

        $this->mockedSmServices = [
            \ZfcRbac\Service\AuthorizationService::class => m::mock(\ZfcRbac\Service\AuthorizationService::class)
        ];

        $this->loggedInUser = m::mock(UserEntity::class)->makePartial();
        $this->mockedSmServices[\ZfcRbac\Service\AuthorizationService::class]
            ->shouldReceive('getIdentity->getUser')->andReturn($this->loggedInUser);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = ['tmap_st_incomplete', 'tm_s_cur'];

        $this->references = [
            \Dvsa\Olcs\Api\Entity\Application\Application::class => [
                863 => m::mock(\Dvsa\Olcs\Api\Entity\Application\Application::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommandAlreadyTmLoggedInUser()
    {
        $command = Command::create(['application' => 863, 'user' => 234, 'action' => 'D']);

        $user = $this->loggedInUser;
        $mockTm = m::mock()
            ->shouldReceive('getId')
            ->andReturn(21)
            ->getMock();

        $user->setTransportManager($mockTm);

        $this->repoMap['User']->shouldReceive('fetchForTma')->with(234)->once()->andReturn($user);
        $this->repoMap['TransportManagerApplication']->shouldReceive('save')->once()->andReturnUsing(
            function (\Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication $tma) {
                $tma->setId(534);

                $this->assertSame('D', $tma->getAction());
                $this->assertSame(
                    $this->references[\Dvsa\Olcs\Api\Entity\Application\Application::class][863],
                    $tma->getApplication()
                );
                $this->assertSame($this->refData['tmap_st_incomplete'], $tma->getTmApplicationStatus());
                $this->assertSame(21, $tma->getTransportManager()->getId());
            }
        )
        ->shouldReceive('fetchByTmAndApplication')
        ->with(21, 863, true)
        ->once()
        ->andReturn(null);

        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion::class,
            [
                'id' => 863,
                'section' => 'transportManagers'
            ],
            new \Dvsa\Olcs\Api\Domain\Command\Result()
        );

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandSetDob()
    {
        $command = Command::create(['application' => 863, 'user' => 234, 'action' => 'D', 'dob' => '2015-11-26']);

        $user = $this->loggedInUser;
        $this->loggedInUser->shouldReceive('getContactDetails->getPerson->setBirthDate')
            ->andReturnUsing(
                function ($date) {
                    $this->assertEquals(new \Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime('2015-11-26'), $date);
                }
            )
            ->once();

        $mockTm = m::mock()
            ->shouldReceive('getId')
            ->andReturn(21)
            ->getMock();

        $user->setTransportManager($mockTm);

        $this->repoMap['User']->shouldReceive('fetchForTma')->with(234)->once()->andReturn($user);
        $this->repoMap['TransportManagerApplication']->shouldReceive('save')->once()
            ->shouldReceive('fetchByTmAndApplication')
            ->with(21, 863, true)
            ->once()
            ->andReturn(null);

        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion::class,
            [
                'id' => 863,
                'section' => 'transportManagers'
            ],
            new \Dvsa\Olcs\Api\Domain\Command\Result()
        );

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandCreateTm()
    {
        $command = Command::create(['application' => 863, 'user' => 234, 'action' => 'D']);

        $user = m::mock(UserEntity::class)->makePartial();
        $user->setContactDetails('CD');

        $this->repoMap['User']->shouldReceive('fetchForTma')->with(234)->once()->andReturn($user);

        $savedTm = null;
        $this->repoMap['TransportManager']->shouldReceive('save')->once()->andReturnUsing(
            function (\Dvsa\Olcs\Api\Entity\Tm\TransportManager $tm) use (&$savedTm) {
                $tm->setId(645);

                $this->assertSame('CD', $tm->getHomeCd());

                $this->assertSame($this->refData['tm_s_cur'], $tm->getTmStatus());

                $savedTm = $tm;
            }
        );
        $this->repoMap['User']->shouldReceive('save')->once()->andReturnUsing(
            function (\Dvsa\Olcs\Api\Entity\User\User $u) use (&$savedTm) {
                $this->assertSame($savedTm, $u->getTransportManager());
            }
        );

        $this->repoMap['TransportManagerApplication']->shouldReceive('save')->once()->andReturnUsing(
            function (\Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication $tma) use (&$savedTm) {
                $tma->setId(534);

                $this->assertSame('D', $tma->getAction());
                $this->assertSame(
                    $this->references[\Dvsa\Olcs\Api\Entity\Application\Application::class][863],
                    $tma->getApplication()
                );
                $this->assertSame($this->refData['tmap_st_incomplete'], $tma->getTmApplicationStatus());
                $this->assertSame($savedTm, $tma->getTransportManager());
            }
        );

        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Email\SendTmApplication::class,
            [
                'id' => 534,
            ],
            new \Dvsa\Olcs\Api\Domain\Command\Result()
        );

        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion::class,
            [
                'id' => 863,
                'section' => 'transportManagers'
            ],
            new \Dvsa\Olcs\Api\Domain\Command\Result()
        );

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandWithException()
    {
        $this->setExpectedException(
            ValidationException::class,
            [
                'registeredUser' => [
                    TransportManagerApplicationEntity::ERROR_TM_EXIST =>
                        'forename familyName has already been added to this application'
                ]
            ]
        );
        $command = Command::create(['application' => 863, 'user' => 234, 'action' => 'D']);

        $mockContactDetails = m::mock()
            ->shouldReceive('getPerson')
            ->andReturn(
                m::mock()
                ->shouldReceive('getForename')
                ->andReturn('forename')
                ->once()
                ->shouldReceive('getFamilyname')
                ->andReturn('familyName')
                ->once()
                ->getMock()
            )
            ->getMock();

        $user = m::mock(UserEntity::class)->makePartial();
        $user->setContactDetails($mockContactDetails);
        $mockTm = m::mock()
            ->shouldReceive('getId')
            ->andReturn(21)
            ->getMock();

        $user->setTransportManager($mockTm);

        $this->repoMap['User']->shouldReceive('fetchForTma')->with(234)->once()->andReturn($user);

        $this->repoMap['TransportManagerApplication']
            ->shouldReceive('fetchByTmAndApplication')
            ->with(21, 863, true)
            ->once()
            ->andReturn(['foo']);

        $this->sut->handleCommand($command);
    }
}
