<?php

/**
 * Create Application Fee Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\CreateApplicationFee;
use Dvsa\Olcs\Api\Domain\Command\Application\CreateApplicationFee as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use Dvsa\Olcs\Api\Domain\Repository\FeeType;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\Application;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Entity\Task\Task;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;

/**
 * Create Application Fee Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CreateApplicationFeeTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateApplicationFee();
        $this->mockRepo('Application', Application::class);
        $this->mockRepo('FeeType', FeeType::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            FeeTypeEntity::FEE_TYPE_APP,
            Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
            Licence::LICENCE_TYPE_STANDARD_NATIONAL,
        ];

        $this->references = [
            TrafficArea::class => [
                TrafficArea::NORTHERN_IRELAND_TRAFFIC_AREA_CODE => m::mock(TrafficArea::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $command = Cmd::create(['id' => 111]);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(222);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setId(111);
        $application->setLicence($licence);
        $application->setNiFlag('Y');
        $application->setGoodsOrPsv($this->refData[Licence::LICENCE_CATEGORY_GOODS_VEHICLE]);
        $application->setLicenceType($this->refData[Licence::LICENCE_TYPE_STANDARD_NATIONAL]);
        $application->setReceivedDate('2015-01-01');

        /** @var FeeTypeEntity $feeType */
        $feeType = m::mock(FeeTypeEntity::class)->makePartial();
        $feeType->setId(444);
        $feeType->setDescription('DESC');
        $feeType->setFixedValue(10.5);

        $this->repoMap['FeeType']->shouldReceive('fetchLatest')
            ->with(
                $this->refData[FeeTypeEntity::FEE_TYPE_APP],
                $this->refData[Licence::LICENCE_CATEGORY_GOODS_VEHICLE],
                $this->refData[Licence::LICENCE_TYPE_STANDARD_NATIONAL],
                m::type('\DateTime'),
                $this->references[TrafficArea::class][TrafficArea::NORTHERN_IRELAND_TRAFFIC_AREA_CODE]
            )
            ->andReturn($feeType);

        $this->repoMap['Application']->shouldReceive('beginTransaction')
            ->once()
            ->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT)
            ->andReturn($application)
            ->shouldReceive('commit')
            ->once();

        $result1 = new Result();
        $result1->addId('task', 333);
        $taskData = [
            'category' => Task::CATEGORY_APPLICATION,
            'subCategory' => Task::SUBCATEGORY_FEE_DUE,
            'description' => 'Application Fee Due',
            'actionDate' => date('Y-m-d'),
            'assignedToUser' => 1,
            'assignedToTeam' => 2,
            'application' => 111,
            'licence' => 222,
            'isClosed' => false,
            'urgent' => false
        ];
        $this->expectedSideEffect(CreateTask::class, $taskData, $result1);

        $result2 = new Result();
        $result2->addId('fee', 555);
        $feeData = [
            'task' => 333,
            'application' => 111,
            'licence' => 222,
            'invoicedDate' => date('Y-m-d'),
            'description' => 'DESC for application 111',
            'feeType' => 444,
            'amount' => 10.5,
            'feeStatus' => FeeEntity::STATUS_OUTSTANDING
        ];
        $this->expectedSideEffect(CreateFee::class, $feeData, $result2);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'task' => 333,
                'fee' => 555
            ],
            'messages' => []
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWithException()
    {
        $command = Cmd::create(['id' => 111]);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(222);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setId(111);
        $application->setLicence($licence);
        $application->setNiFlag('Y');
        $application->setGoodsOrPsv($this->refData[Licence::LICENCE_CATEGORY_GOODS_VEHICLE]);
        $application->setLicenceType($this->refData[Licence::LICENCE_TYPE_STANDARD_NATIONAL]);
        $application->setReceivedDate('2015-01-01');

        /** @var FeeTypeEntity $feeType */
        $feeType = m::mock(FeeTypeEntity::class)->makePartial();
        $feeType->setId(444);
        $feeType->setDescription('DESC');
        $feeType->setFixedValue(10.5);

        $this->repoMap['FeeType']->shouldReceive('fetchLatest')
            ->with(
                $this->refData[FeeTypeEntity::FEE_TYPE_APP],
                $this->refData[Licence::LICENCE_CATEGORY_GOODS_VEHICLE],
                $this->refData[Licence::LICENCE_TYPE_STANDARD_NATIONAL],
                m::type('\DateTime'),
                $this->references[TrafficArea::class][TrafficArea::NORTHERN_IRELAND_TRAFFIC_AREA_CODE]
            )
            ->andReturn($feeType);

        $this->repoMap['Application']->shouldReceive('beginTransaction')
            ->once()
            ->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT)
            ->andReturn($application)
            ->shouldReceive('commit')
            ->once()
            ->andThrow('\Exception')
            ->shouldReceive('rollback')
            ->once();

        $this->setExpectedException('\Exception');

        $result1 = new Result();
        $result1->addId('task', 333);
        $taskData = [
            'category' => Task::CATEGORY_APPLICATION,
            'subCategory' => Task::SUBCATEGORY_FEE_DUE,
            'description' => 'Application Fee Due',
            'actionDate' => date('Y-m-d'),
            'assignedToUser' => 1,
            'assignedToTeam' => 2,
            'application' => 111,
            'licence' => 222,
            'isClosed' => false,
            'urgent' => false
        ];
        $this->expectedSideEffect(CreateTask::class, $taskData, $result1);

        $result2 = new Result();
        $result2->addId('fee', 555);
        $feeData = [
            'task' => 333,
            'application' => 111,
            'licence' => 222,
            'invoicedDate' => date('Y-m-d'),
            'description' => 'DESC for application 111',
            'feeType' => 444,
            'amount' => 10.5,
            'feeStatus' => FeeEntity::STATUS_OUTSTANDING
        ];
        $this->expectedSideEffect(CreateFee::class, $feeData, $result2);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'task' => 333,
                'fee' => 555
            ],
            'messages' => []
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
