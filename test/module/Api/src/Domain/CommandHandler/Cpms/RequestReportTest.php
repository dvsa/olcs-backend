<?php

/**
 * Cpms Request Report Command Handler Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Cpms;

use Dvsa\Olcs\Api\Domain\Command\Queue\Create as CreateQueueCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cpms\RequestReport;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Api\Service\CpmsHelperInterface as CpmsHelper;
use Dvsa\Olcs\Transfer\Command\Cpms\RequestReport as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 *  Cpms Request Report Command Handler Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class RequestReportTest extends CommandHandlerTestCase
{
    protected $mockApi;

    public function setUp()
    {
        $this->mockCpmsService = m::mock(CpmsHelper::class);

        $this->mockedSmServices = [
            'CpmsHelperService' => $this->mockCpmsService,
            AuthorizationService::class => m::mock(AuthorizationService::class)->makePartial(),
        ];

        $this->sut = new RequestReport();

        /** @var UserEntity $mockUser */
        $mockUser = m::mock(UserEntity::class)
            ->shouldReceive('getId')
            ->andReturn(123)
            ->getMock();

        $this->mockedSmServices[AuthorizationService::class]
            ->shouldReceive('getIdentity->getUser')
            ->andReturn($mockUser);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $reportCode = 'FOO';
        $start = '2015-10-07 12:34:56';
        $end = '2015-10-08 12:34:55';

        // expectations
        $this->mockCpmsService
            ->shouldReceive('requestReport')
            ->once()
            ->with($reportCode, m::type(\DateTime::class), m::type(\DateTime::class))
            ->andReturnUsing(
                function ($code, $startDatetime, $endDatetime) use ($start, $end) {
                    $this->assertEquals($start, $startDatetime->format('Y-m-d H:i:s'));
                    $this->assertEquals($end, $endDatetime->format('Y-m-d H:i:s'));
                    return [
                        'reference' => 'OLCS-1234-FOO',
                    ];
                }
            );

        $queueResult = new Result();
        $queueResult
            ->addId('queue', 99)
            ->addMessage('Queue created');
        $this->expectedSideEffect(
            CreateQueueCmd::class,
            [
                'type' => Queue::TYPE_CPMS_REPORT_DOWNLOAD,
                'status' => Queue::STATUS_QUEUED,
                'user' => 123,
                'options' => '{"reference":"OLCS-1234-FOO"}',
            ],
            $queueResult
        );

        // invoke
        $command = Cmd::create(
            [
                'reportCode' => $reportCode,
                'start' => $start,
                'end' => $end,
            ]
        );
        $result = $this->sut->handleCommand($command);

        // assertions
        $this->assertEquals(['Queue created', 'Report requested'], $result->getMessages());
        $this->assertEquals(['queue' => 99, 'cpmsReport' => 'OLCS-1234-FOO'], $result->getIds());
    }
}
