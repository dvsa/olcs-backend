<?php

/**
 * CreateTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Fee;

use Dvsa\Olcs\Api\Domain\CommandHandler\TransportManagerApplication\Create as CommandHandler;
use Dvsa\Olcs\Api\Domain\Repository\TransportManager;
use Dvsa\Olcs\Api\Domain\Repository\TransportManagerApplication;
use Dvsa\Olcs\Api\Domain\Repository\User;
use Dvsa\Olcs\Transfer\Command\TransportManagerApplication\Create as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

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

        $this->loggedInUser = new \Dvsa\Olcs\Api\Entity\User\User();
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
        $user->setTransportManager('TM');

        $this->repoMap['User']->shouldReceive('fetchById')->with(234)->once()->andReturn($user);
        $this->repoMap['TransportManagerApplication']->shouldReceive('save')->once()->andReturnUsing(
            function (\Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication $tma) {
                $tma->setId(534);

                $this->assertSame('D', $tma->getAction());
                $this->assertSame(
                    $this->references[\Dvsa\Olcs\Api\Entity\Application\Application::class][863],
                    $tma->getApplication()
                );
                $this->assertSame($this->refData['tmap_st_incomplete'], $tma->getTmApplicationStatus());
                $this->assertSame('TM', $tma->getTransportManager());
            }
        );

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandCreateTm()
    {
        $command = Command::create(['application' => 863, 'user' => 234, 'action' => 'D']);

        $user = new \Dvsa\Olcs\Api\Entity\User\User();
        $user->setContactDetails('CD');

        $this->repoMap['User']->shouldReceive('fetchById')->with(234)->once()->andReturn($user);

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
            function(\Dvsa\Olcs\Api\Entity\User\User $u) use (&$savedTm) {
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

        $this->sut->handleCommand($command);
    }
}
