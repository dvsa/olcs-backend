<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\UpdateVehicleDeclaration as CommandHandler;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Application\UpdateVehicleDeclaration as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion as UpdateApplicationCompletionCmd;

/**
 * UpdateVehicleDeclarationTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class UpdateVehicleDeclarationTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Application', \Dvsa\Olcs\Api\Domain\Repository\Application::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            'ltyp_r',
        ];

        parent::initReferences();
    }

    protected function setupApplication($noSmall, $noMedium, $noLarge)
    {
        $licence = new \Dvsa\Olcs\Api\Entity\Licence\Licence(
            new \Dvsa\Olcs\Api\Entity\Organisation\Organisation(),
            new \Dvsa\Olcs\Api\Entity\System\RefData()
        );

        $application = new \Dvsa\Olcs\Api\Entity\Application\Application(
            $licence,
            new \Dvsa\Olcs\Api\Entity\System\RefData(),
            1
        );

        $application->setId(423);
        $application->setLicenceType($this->refData['ltyp_r']);
        $application->setTotAuthLargeVehicles($noLarge);
        $application->setTotAuthMediumVehicles($noMedium);
        $application->setTotAuthSmallVehicles($noSmall);

        return $application;
    }

    public function testHandleCommand()
    {
        $command = Cmd::create(
            [
                'id' => 423,
                'version' => 32,
                'psvNoSmallVhlConfirmation' => 'Y',
                'psvOperateSmallVhl' => 'Y',
                'psvSmallVhlConfirmation' => 'Y',
                'psvSmallVhlNotes' => 'Y',
                'psvMediumVhlConfirmation' => 'Y',
                'psvMediumVhlNotes' => 'Y',
                'psvLimousines' => 'Y',
                'psvNoLimousineConfirmation' => 'Y',
                'psvOnlyLimousinesConfirmation' => 'Y',
            ]
        );

        $application = $this->setupApplication(0, 0, 0);
        $this->repoMap['Application']->shouldReceive('fetchUsingId')->once()->with($command, Query::HYDRATE_OBJECT, 32)
            ->andReturn($application)
            ->shouldReceive('save')->with($application)->once();

        $this->expectedSideEffect(
            UpdateApplicationCompletionCmd::class, ['id' => 423, 'section' => 'vehicles_declarations'], new Result()
        );

        $this->sut->handleCommand($command);

        $this->assertSame('Y', $application->getPsvNoSmallVhlConfirmation());
        $this->assertSame('Y', $application->getPsvOperateSmallVhl());
        $this->assertSame('Y', $application->getPsvSmallVhlConfirmation());
        $this->assertSame('Y', $application->getPsvSmallVhlNotes());
        $this->assertSame('Y', $application->getPsvMediumVhlConfirmation());
        $this->assertSame('Y', $application->getPsvMediumVhlNotes());
        $this->assertSame('Y', $application->getPsvLimousines());
        $this->assertSame('Y', $application->getPsvNoLimousineConfirmation());
        $this->assertSame('Y', $application->getPsvOnlyLimousinesConfirmation());
    }

    public function testHandleCommandValidateMainOccupation()
    {
        $command = Cmd::create(
            [
                'id' => 423,
                'version' => 32,
                'psvNoSmallVhlConfirmation' => 'Y',
                'psvOperateSmallVhl' => 'Y',
                'psvSmallVhlConfirmation' => 'Y',
                'psvSmallVhlNotes' => 'Y',
                'psvMediumVhlConfirmation' => 'N',
                'psvMediumVhlNotes' => '',
                'psvLimousines' => 'Y',
                'psvNoLimousineConfirmation' => 'Y',
                'psvOnlyLimousinesConfirmation' => 'Y',
            ]
        );

        $application = $this->setupApplication(0, 10, 0);
        $this->repoMap['Application']->shouldReceive('fetchUsingId')->once()->with($command, Query::HYDRATE_OBJECT, 32)
            ->andReturn($application);

        try {
            $this->sut->handleCommand($command);
        } catch (\Dvsa\Olcs\Api\Domain\Exception\ValidationException $e) {

            $this->assertSame(
                [
                    'psvMediumVhlConfirmation' => 'psvMediumVhlConfirmation must be Y',
                    'psvMediumVhlNotes' => 'psvMediumVhlNotes must be not be empty'
                ],
                $e->getMessages()
            );
        }
    }

    public function testHandleCommandValidateSmall1()
    {
        $command = Cmd::create(
            [
                'id' => 423,
                'version' => 32,
                'psvNoSmallVhlConfirmation' => 'Y',
                'psvOperateSmallVhl' => 'Y',
                'psvSmallVhlConfirmation' => 'Y',
                'psvSmallVhlNotes' => '',
                'psvMediumVhlConfirmation' => 'N',
                'psvMediumVhlNotes' => '',
                'psvLimousines' => 'Y',
                'psvNoLimousineConfirmation' => 'Y',
                'psvOnlyLimousinesConfirmation' => 'Y',
            ]
        );

        $application = $this->setupApplication(10, 0, 0);
        $this->repoMap['Application']->shouldReceive('fetchUsingId')->once()->with($command, Query::HYDRATE_OBJECT, 32)
            ->andReturn($application);

        try {
            $this->sut->handleCommand($command);
        } catch (\Dvsa\Olcs\Api\Domain\Exception\ValidationException $e) {

            $this->assertSame(['psvSmallVhlNotes' => 'psvSmallVhlNotes must be not be empty'], $e->getMessages());
        }
    }

    public function testHandleCommandValidateSmall2()
    {
        $command = Cmd::create(
            [
                'id' => 423,
                'version' => 32,
                'psvNoSmallVhlConfirmation' => 'Y',
                'psvOperateSmallVhl' => 'N',
                'psvSmallVhlConfirmation' => 'N',
                'psvSmallVhlNotes' => '',
                'psvMediumVhlConfirmation' => 'N',
                'psvMediumVhlNotes' => '',
                'psvLimousines' => 'Y',
                'psvNoLimousineConfirmation' => 'Y',
                'psvOnlyLimousinesConfirmation' => 'Y',
            ]
        );

        $application = $this->setupApplication(10, 0, 0);
        $this->repoMap['Application']->shouldReceive('fetchUsingId')->once()->with($command, Query::HYDRATE_OBJECT, 32)
            ->andReturn($application);

        try {
            $this->sut->handleCommand($command);
        } catch (\Dvsa\Olcs\Api\Domain\Exception\ValidationException $e) {

            $this->assertSame(['psvSmallVhlConfirmation' => 'psvSmallVhlConfirmation must be Y'], $e->getMessages());
        }
    }

    public function testHandleCommandValidateNineOrMore()
    {
        $command = Cmd::create(
            [
                'id' => 423,
                'version' => 32,
                'psvNoSmallVhlConfirmation' => 'N',
                'psvOperateSmallVhl' => 'N',
                'psvSmallVhlConfirmation' => 'N',
                'psvSmallVhlNotes' => '',
                'psvMediumVhlConfirmation' => 'N',
                'psvMediumVhlNotes' => '',
                'psvLimousines' => 'Y',
                'psvNoLimousineConfirmation' => 'Y',
                'psvOnlyLimousinesConfirmation' => 'Y',
            ]
        );

        $application = $this->setupApplication(0, 0, 0);
        $this->repoMap['Application']->shouldReceive('fetchUsingId')->once()->with($command, Query::HYDRATE_OBJECT, 32)
            ->andReturn($application);

        try {
            $this->sut->handleCommand($command);
        } catch (\Dvsa\Olcs\Api\Domain\Exception\ValidationException $e) {

            $this->assertSame(
                ['psvNoSmallVhlConfirmation' => 'psvNoSmallVhlConfirmation must be Y'],
                $e->getMessages()
            );
        }
    }

    public function testHandleCommandValidateLimousines1()
    {
        $command = Cmd::create(
            [
                'id' => 423,
                'version' => 32,
                'psvNoSmallVhlConfirmation' => 'Y',
                'psvOperateSmallVhl' => 'N',
                'psvSmallVhlConfirmation' => 'Y',
                'psvSmallVhlNotes' => '',
                'psvMediumVhlConfirmation' => 'Y',
                'psvMediumVhlNotes' => 'X',
                'psvLimousines' => 'Y',
                'psvNoLimousineConfirmation' => 'Y',
                'psvOnlyLimousinesConfirmation' => 'N',
            ]
        );

        $application = $this->setupApplication(0, 10, 0);
        $this->repoMap['Application']->shouldReceive('fetchUsingId')->once()->with($command, Query::HYDRATE_OBJECT, 32)
            ->andReturn($application);

        try {
            $this->sut->handleCommand($command);
        } catch (\Dvsa\Olcs\Api\Domain\Exception\ValidationException $e) {

            $this->assertSame(
                ['psvOnlyLimousinesConfirmation' => 'psvOnlyLimousinesConfirmation must be Y'],
                $e->getMessages()
            );
        }
    }

    public function testHandleCommandValidateLimousines2()
    {
        $command = Cmd::create(
            [
                'id' => 423,
                'version' => 32,
                'psvNoSmallVhlConfirmation' => 'Y',
                'psvOperateSmallVhl' => 'N',
                'psvSmallVhlConfirmation' => 'Y',
                'psvSmallVhlNotes' => '',
                'psvMediumVhlConfirmation' => 'Y',
                'psvMediumVhlNotes' => 'X',
                'psvLimousines' => 'N',
                'psvNoLimousineConfirmation' => 'N',
                'psvOnlyLimousinesConfirmation' => 'N',
            ]
        );

        $application = $this->setupApplication(0, 10, 0);
        $this->repoMap['Application']->shouldReceive('fetchUsingId')->once()->with($command, Query::HYDRATE_OBJECT, 32)
            ->andReturn($application);

        try {
            $this->sut->handleCommand($command);
        } catch (\Dvsa\Olcs\Api\Domain\Exception\ValidationException $e) {

            $this->assertSame(
                ['psvNoLimousineConfirmation' => 'psvNoLimousineConfirmation must be Y'],
                $e->getMessages()
            );
        }
    }
}
