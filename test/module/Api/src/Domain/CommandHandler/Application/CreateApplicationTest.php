<?php

/**
 * Create Application Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Application\CreateApplicationFee;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion;
use Dvsa\Olcs\Api\Entity\Application\ApplicationTracking;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\CreateApplication;
use Dvsa\Olcs\Api\Domain\Repository\Application;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Application\CreateApplication as Cmd;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;

/**
 * Create Application Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CreateApplicationTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateApplication();
        $this->mockRepo('Application', Application::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            Licence::LICENCE_STATUS_NOT_SUBMITTED,
            ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION,
            Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            Licence::LICENCE_CATEGORY_GOODS_VEHICLE
        ];

        $this->references = [
            Organisation::class => [
                11 => m::mock(Organisation::class)
            ],
            TrafficArea::class => [
                TrafficArea::NORTH_EASTERN_TRAFFIC_AREA_CODE => m::mock(TrafficArea::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommandMinimal()
    {
        $command = Cmd::create(['organisation' => 11]);
        /** @var ApplicationEntity $app */
        $app = null;

        $this->repoMap['Application']->shouldReceive('beginTransaction')
            ->once()
            ->shouldReceive('save')
            ->with(m::type(ApplicationEntity::class))
            ->andReturnUsing(
                function (ApplicationEntity $application) use (&$app) {
                    $app = $application;
                    $application->setId(22);
                    $application->getLicence()->setId(33);
                }
            )
            ->shouldReceive('commit')
            ->once();

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'application' => 22,
                'licence' => 33
            ],
            'messages' => [
                'Licence created',
                'Application created',
                'Application Completion created',
                'Application Tracking created',

            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertInstanceOf(ApplicationEntity::class, $app);
        $this->assertInstanceOf(ApplicationCompletion::class, $app->getApplicationCompletion());
        $this->assertInstanceOf(ApplicationTracking::class, $app->getApplicationTracking());
        $this->assertInstanceOf(Licence::class, $app->getLicence());
        $this->assertSame($this->references[Organisation::class][11], $app->getLicence()->getOrganisation());
        $this->assertSame($this->refData[Licence::LICENCE_STATUS_NOT_SUBMITTED], $app->getLicence()->getStatus());
        $this->assertSame($this->refData[ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION], $app->getStatus());

        $this->assertNull($app->getReceivedDate());
        $this->assertNull($app->getNiFlag());
        $this->assertNull($app->getLicenceType());
        $this->assertNull($app->getGoodsOrPsv());
        $this->assertNull($app->getLicence()->getTrafficArea());
    }

    public function testHandleCommand()
    {
        $command = Cmd::create(
            [
                'organisation' => 11,
                'receivedDate' => '2015-01-01',
                'trafficArea' => TrafficArea::NORTH_EASTERN_TRAFFIC_AREA_CODE,
                'niFlag' => 'Y',
                'operatorType' => Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
                'licenceType' => Licence::LICENCE_TYPE_STANDARD_NATIONAL
            ]
        );
        /** @var ApplicationEntity $app */
        $app = null;

        $this->repoMap['Application']->shouldReceive('beginTransaction')
            ->once()
            ->shouldReceive('save')
            ->with(m::type(ApplicationEntity::class))
            ->andReturnUsing(
                function (ApplicationEntity $application) use (&$app) {
                    $app = $application;
                    $application->setId(22);
                    $application->getLicence()->setId(33);
                }
            )
            ->shouldReceive('commit')
            ->once();

        $result1 = new Result();
        $result1->addId('fee', 44);
        $this->expectedSideEffect(CreateApplicationFee::class, ['id' => 22], $result1);

        $result2 = new Result();
        $result2->addMessage('Section updated');
        $this->expectedSideEffect(
            UpdateApplicationCompletion::class,
            ['id' => 22, 'section' => 'typeOfLicence'],
            $result2
        );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'application' => 22,
                'licence' => 33,
                'fee' => 44
            ],
            'messages' => [
                'Licence created',
                'Application created',
                'Application Completion created',
                'Application Tracking created',
                'Section updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertInstanceOf(ApplicationEntity::class, $app);
        $this->assertInstanceOf(ApplicationCompletion::class, $app->getApplicationCompletion());
        $this->assertInstanceOf(ApplicationTracking::class, $app->getApplicationTracking());
        $this->assertInstanceOf(Licence::class, $app->getLicence());
        $this->assertSame($this->references[Organisation::class][11], $app->getLicence()->getOrganisation());
        $this->assertSame($this->refData[Licence::LICENCE_STATUS_NOT_SUBMITTED], $app->getLicence()->getStatus());
        $this->assertSame($this->refData[ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION], $app->getStatus());

        $this->assertInstanceOf('\DateTime', $app->getReceivedDate());
        $this->assertEquals('2015-01-01', $app->getReceivedDate()->format('Y-m-d'));
        $this->assertEquals('Y', $app->getNiFlag());
        $this->assertSame($this->refData[Licence::LICENCE_TYPE_STANDARD_NATIONAL], $app->getLicenceType());
        $this->assertSame($this->refData[Licence::LICENCE_CATEGORY_GOODS_VEHICLE], $app->getGoodsOrPsv());
        $this->assertSame(
            $this->references[TrafficArea::class][TrafficArea::NORTH_EASTERN_TRAFFIC_AREA_CODE],
            $app->getLicence()->getTrafficArea()
        );
    }

    public function testHandleCommandMinimalException()
    {
        $command = Cmd::create(['organisation' => 11]);

        $this->repoMap['Application']->shouldReceive('beginTransaction')
            ->once()
            ->shouldReceive('save')
            ->with(m::type(ApplicationEntity::class))
            ->andThrow('\Exception')
            ->shouldReceive('rollback')
            ->once();

        $this->setExpectedException('\Exception');

        $this->sut->handleCommand($command);
    }
}
