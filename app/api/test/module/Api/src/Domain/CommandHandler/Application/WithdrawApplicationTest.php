<?php

/**
 * WithdrawApplication.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Licence\ReturnAllCommunityLicences;
use Dvsa\Olcs\Transfer\Command\Application\CreateSnapshot;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\WithdrawApplication as CommandHandler;
use Dvsa\Olcs\Transfer\Command\Application\WithdrawApplication as Command;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Domain\Command\Discs\CeaseGoodsDiscsForApplication;
use Dvsa\Olcs\Api\Domain\Command\Licence\Withdraw;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\LicenceVehicle as LicenceVehicleRepo;
use Dvsa\Olcs\Api\Domain\Command\Application\EndInterim as EndInterimCmd;

/**
 * Withdraw Application Test
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class WithdrawApplicationTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Application', ApplicationRepo::class);
        $this->mockRepo('LicenceVehicle', LicenceVehicleRepo::class);

        $this->mockedSmServices = [
            \ZfcRbac\Service\AuthorizationService::class => m::mock(\ZfcRbac\Service\AuthorizationService::class)
        ];

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            'apsts_withdrawn', 'withdrawn'
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $command = Command::create(['id' => 532, 'reason' => 'withdrawn']);

        $this->setupIsInternalUser(false);

        $trafficArea = new \Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea();
        $trafficArea->setId('TA');

        $licence = m::mock(Licence::class)
            ->shouldReceive('getId')
            ->andReturn(123)
            ->shouldReceive('getCommunityLics')
            ->andReturn(
                m::mock()
                    ->shouldReceive('count')
                    ->once()
                    ->andReturn(3)
                    ->getMock()
            )
            ->shouldReceive('getTrafficArea')->with()->once()->andReturn($trafficArea)
            ->getMock();

        $application = m::mock(Application::class)->makePartial();
        $application->setId(1);
        $application->setLicence($licence);

        $application->shouldReceive('getCurrentInterimStatus')
            ->andReturn(Application::INTERIM_STATUS_INFORCE)
            ->once()
            ->shouldReceive('isGoods')->andReturn(true)
            ->getMock();
        $this->expectedSideEffect(EndInterimCmd::class, ['id' => 1], new Result());

        $application->shouldReceive('getIsVariation')->andReturn(false);

        $this->repoMap['Application']->shouldReceive('fetchById')
            ->with(532)
            ->andReturn($application)
            ->shouldReceive('save')
            ->once()
            ->with(m::type(Application::class));

        $this->repoMap['LicenceVehicle']->shouldReceive('clearSpecifiedDateAndInterimApp')
            ->with($application)
            ->once()
            ->getMock();

        $this->expectedSideEffect(Withdraw::class, ['id' => 123], new Result());
        $this->expectedSideEffect(CeaseGoodsDiscsForApplication::class, ['application' => 1], new Result());

        $this->expectedSideEffect(
            \Dvsa\Olcs\Transfer\Command\Publication\Application::class,
            ['id' => 1, 'trafficArea' => 'TA'],
            new Result()
        );
        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Application\CloseTexTask::class,
            ['id' => 1],
            new Result()
        );

        $this->expectedSideEffect(
            \Dvsa\Olcs\Transfer\Command\Application\Schedule41Cancel::class,
            ['id' => 1],
            new Result()
        );

        $result1 = new Result();
        $result1->addMessage('Snapshot created');
        $data = ['id' => 532, 'event' => CreateSnapshot::ON_WITHDRAW];
        $this->expectedSideEffect(CreateSnapshot::class, $data, $result1);

        $this->expectedSideEffect(ReturnAllCommunityLicences::class, ['id' => 123], new Result());

        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Application\CancelOutstandingFees::class,
            ['id' => 1],
            new \Dvsa\Olcs\Api\Domain\Command\Result()
        );

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['Snapshot created', 'Application 1 withdrawn.'], $result->getMessages());
    }

    public function testHandleCommandCloseTasks()
    {
        $command = Command::create(['id' => 532, 'reason' => 'withdrawn']);

        $this->setupIsInternalUser(true);

        $trafficArea = new \Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea();
        $trafficArea->setId('TA');

        $licence = m::mock(Licence::class)
            ->shouldReceive('getId')
            ->andReturn(123)
            ->shouldReceive('getCommunityLics')
            ->andReturn(
                m::mock()
                    ->shouldReceive('count')
                    ->once()
                    ->andReturn(3)
                    ->getMock()
            )
            ->shouldReceive('getTrafficArea')->with()->once()->andReturn($trafficArea)
            ->getMock();

        $application = m::mock(Application::class)->makePartial();
        $application->setId(1);
        $application->setLicence($licence);

        $application->shouldReceive('getCurrentInterimStatus')
            ->andReturn(Application::INTERIM_STATUS_INFORCE)
            ->once()
            ->shouldReceive('isGoods')
            ->andReturn(true)
            ->getMock();
        $this->expectedSideEffect(EndInterimCmd::class, ['id' => 1], new Result());

        $application->shouldReceive('getIsVariation')->andReturn(false);

        $this->repoMap['Application']->shouldReceive('fetchById')
            ->with(532)
            ->andReturn($application)
            ->shouldReceive('save')
            ->once()
            ->with(m::type(Application::class));

        $this->repoMap['LicenceVehicle']->shouldReceive('clearSpecifiedDateAndInterimApp')
            ->with($application)
            ->once()
            ->getMock();

        $this->expectedSideEffect(Withdraw::class, ['id' => 123], new Result());
        $this->expectedSideEffect(CeaseGoodsDiscsForApplication::class, ['application' => 1], new Result());

        $this->expectedSideEffect(
            \Dvsa\Olcs\Transfer\Command\Publication\Application::class,
            ['id' => 1, 'trafficArea' => 'TA'],
            new Result()
        );
        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Application\CloseTexTask::class,
            ['id' => 1],
            new Result()
        );

        $result1 = new Result();
        $result1->addMessage('Snapshot created');
        $data = ['id' => 532, 'event' => CreateSnapshot::ON_WITHDRAW];
        $this->expectedSideEffect(CreateSnapshot::class, $data, $result1);

        $this->expectedSideEffect(ReturnAllCommunityLicences::class, ['id' => 123], new Result());

        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Application\CloseTexTask::class,
            ['id' => 1],
            (new Result())->addMessage('CLOSE_TEX_TASK')
        );
        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Application\CloseFeeDueTask::class,
            ['id' => 1],
            (new Result())->addMessage('CLOSE_FEEDUE_TASK')
        );

        $this->expectedSideEffect(
            \Dvsa\Olcs\Transfer\Command\Application\Schedule41Cancel::class,
            ['id' => 1],
            new Result()
        );

        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Application\CancelOutstandingFees::class,
            ['id' => 1],
            new \Dvsa\Olcs\Api\Domain\Command\Result()
        );

        $result = $this->sut->handleCommand($command);

        $this->assertSame(
            [
                'Snapshot created',
                'CLOSE_TEX_TASK',
                'CLOSE_FEEDUE_TASK',
                'Application 1 withdrawn.'
            ],
            $result->getMessages()
        );
    }

    public function testHandleCommandVariationNotPublishable()
    {
        $command = Command::create(['id' => 532, 'reason' => 'withdrawn']);

        $this->setupIsInternalUser(false);

        $licence = $this->getTestingLicence()
            ->setLicenceType(new \Dvsa\Olcs\Api\Entity\System\RefData(Licence::LICENCE_TYPE_STANDARD_NATIONAL));
        $licence->setId(123);

        $application = $this->getTestingApplication($licence)
            ->setId(1)
            ->setIsVariation(true)
            ->setLicenceType(new \Dvsa\Olcs\Api\Entity\System\RefData(Licence::LICENCE_TYPE_STANDARD_NATIONAL));

        $this->repoMap['Application']->shouldReceive('fetchById')->with(532)->andReturn($application);
        $this->repoMap['Application']->shouldReceive('save')->once()->with(m::type(Application::class));

        $this->repoMap['LicenceVehicle']->shouldReceive('clearSpecifiedDateAndInterimApp')
            ->with($application)
            ->once()
            ->getMock();

        $this->expectedSideEffect(
            CreateSnapshot::class,
            ['id' => 532, 'event' => CreateSnapshot::ON_WITHDRAW],
            (new Result())->addMessage('Snapshot created')
        );
        $this->expectedSideEffect(
            CeaseGoodsDiscsForApplication::class,
            ['application' => 1],
            new Result()
        );

        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Application\CancelOutstandingFees::class,
            ['id' => 1],
            new \Dvsa\Olcs\Api\Domain\Command\Result()
        );

        $result = $this->sut->handleCommand($command);
        $this->assertSame(['Snapshot created', 'Application 1 withdrawn.'], $result->getMessages());
    }

    public function testHandleCommandVariationPublishable()
    {
        $command = Command::create(['id' => 532, 'reason' => 'withdrawn']);

        $this->setupIsInternalUser(false);

        $trafficArea = new \Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea();
        $trafficArea->setId('TA');

        $licence = $this->getTestingLicence()
            ->setTrafficArea($trafficArea)
            ->setLicenceType(new \Dvsa\Olcs\Api\Entity\System\RefData(Licence::LICENCE_TYPE_STANDARD_NATIONAL));
        $licence->setId(123);

        $application = $this->getTestingApplication($licence)
            ->setId(1)
            ->setIsVariation(true)
            ->setLicenceType(new \Dvsa\Olcs\Api\Entity\System\RefData(Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL));

        $this->repoMap['Application']->shouldReceive('fetchById')->with(532)->andReturn($application);
        $this->repoMap['Application']->shouldReceive('save')->once()->with(m::type(Application::class));

        $this->repoMap['LicenceVehicle']->shouldReceive('clearSpecifiedDateAndInterimApp')
            ->with($application)
            ->once()
            ->getMock();

        $this->expectedSideEffect(
            CreateSnapshot::class,
            ['id' => 532, 'event' => CreateSnapshot::ON_WITHDRAW],
            (new Result())->addMessage('Snapshot created')
        );
        $this->expectedSideEffect(
            \Dvsa\Olcs\Transfer\Command\Publication\Application::class,
            ['id' => 1, 'trafficArea' => 'TA'],
            new Result()
        );
        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Application\CloseTexTask::class,
            ['id' => 1],
            new Result()
        );
        $this->expectedSideEffect(
            CeaseGoodsDiscsForApplication::class,
            ['application' => 1],
            new Result()
        );
        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Application\CancelOutstandingFees::class,
            ['id' => 1],
            new \Dvsa\Olcs\Api\Domain\Command\Result()
        );

        $result = $this->sut->handleCommand($command);
        $this->assertSame(['Snapshot created', 'Application 1 withdrawn.'], $result->getMessages());
    }
}
