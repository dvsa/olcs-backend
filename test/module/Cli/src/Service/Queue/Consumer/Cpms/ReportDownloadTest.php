<?php

/**
 * Cpms Report Download Queue Consumer Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Cli\Service\Queue\Consumer\Cpms;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\Cpms\ReportDownload as Sut;
use Dvsa\OlcsTest\Cli\Service\Queue\Consumer\AbstractConsumerTestCase;
use Dvsa\Olcs\Api\Entity\User\User;

/**
 * Cpms Report Download Queue Consumer Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ReportDownloadTest extends AbstractConsumerTestCase
{
    protected $consumerClass = Sut::class;

    public function testProcessMessageSuccess()
    {
        $user = new User('pid', 'type');
        $user->setId(1);
        $item = new QueueEntity();
        $item->setId(99);
        $item->setAttempts(1);
        $item->setOptions('{"reference":"OLCS-1234-ABCD", "name": "FILENAME"}');
        $item->setCreatedBy($user);

        $expectedQryData = ['reference' => 'OLCS-1234-ABCD'];
        $qryResult = [
            'completed' => true,
            'token' => 'secrettoken',
            'extension' => 'csv',
        ];
        $this->expectQuery(
            \Dvsa\Olcs\Transfer\Query\Cpms\ReportStatus::class,
            $expectedQryData,
            $qryResult
        );

        $expectedCmdData = [
            'reference' => 'OLCS-1234-ABCD',
            'token'     => 'secrettoken',
            'filename'  => 'FILENAME.csv',
            'user'      => 1
        ];
        $cmdResult = new Result();
        $cmdResult
            ->addId(
                'identifier',
                "documents/Licensing/Financial_reports/2015/10/20151007110722__Daily_Balance_Report.csv"
            )
            ->addId('document', 666)
            ->addMessage('Report downloaded')
            ->addMessage('File uploaded')
            ->addMessage('Document created');

        $this->expectCommand(
            \Dvsa\Olcs\Transfer\Command\Cpms\DownloadReport::class,
            $expectedCmdData,
            $cmdResult
        );

        $this->expectCommand(
            \Dvsa\Olcs\Api\Domain\Command\Queue\Complete::class,
            ['item' => $item],
            new Result()
        );

        $result = $this->sut->processMessage($item);

        $this->assertEquals(
            'Successfully processed message: 99 {"reference":"OLCS-1234-ABCD", "name": "FILENAME"} Download using '
            . 'reference OLCS-1234-ABCD and token secrettoken and extension csv|Report downloaded|File '
            . 'uploaded|Document created',
            $result
        );
    }

    public function testProcessMessageMaxAttemptsExceeded()
    {
        $user = new User('pid', 'type');
        $user->setId(1);
        $item = new QueueEntity();
        $item->setId(99);
        $item->setAttempts(11);
        $item->setOptions('{"reference":"OLCS-1234-ABCD"}');
        $item->setCreatedBy($user);

        $this->expectCommand(
            \Dvsa\Olcs\Api\Domain\Command\Queue\Failed::class,
            ['item' => $item],
            new Result()
        );

        $result = $this->sut->processMessage($item);

        $this->assertEquals(
            'Failed to process message: 99 {"reference":"OLCS-1234-ABCD"} Maximum attempts exceeded',
            $result
        );
    }

    public function testProcessMessageReportNotReady()
    {
        $user = new User('pid', 'type');
        $user->setId(1);
        $item = new QueueEntity();
        $item->setId(99);
        $item->setOptions('{"reference":"OLCS-1234-ABCD"}');
        $item->setCreatedBy($user);

        $expectedQryData = ['reference' => 'OLCS-1234-ABCD'];
        $expectedException = new \Dvsa\Olcs\Api\Domain\Exception\NotReadyException('try again later');
        $expectedException->setRetryAfter(60);
        $this->expectQueryException(
            \Dvsa\Olcs\Transfer\Query\Cpms\ReportStatus::class,
            $expectedQryData,
            $expectedException
        );

        $this->expectCommand(
            \Dvsa\Olcs\Api\Domain\Command\Queue\Retry::class,
            ['item' => $item, 'retryAfter' => 60],
            new Result()
        );

        $result = $this->sut->processMessage($item);

        $this->assertEquals(
            'Requeued message: 99 {"reference":"OLCS-1234-ABCD"} for retry in 60',
            $result
        );
    }

    public function testProcessMessageFailureFromStatusCheck()
    {
        $user = new User('pid', 'type');
        $user->setId(1);
        $item = new QueueEntity();
        $item->setId(99);
        $item->setOptions('{"reference":"OLCS-1234-ABCD"}');
        $item->setCreatedBy($user);

        $expectedQryData = ['reference' => 'OLCS-1234-ABCD'];
        $this->expectQueryException(
            \Dvsa\Olcs\Transfer\Query\Cpms\ReportStatus::class,
            $expectedQryData,
            \Exception::class,
            'unknown fail'
        );

        $this->expectCommand(
            \Dvsa\Olcs\Api\Domain\Command\Queue\Failed::class,
            ['item' => $item],
            new Result()
        );

        $result = $this->sut->processMessage($item);

        $this->assertEquals(
            'Failed to process message: 99 {"reference":"OLCS-1234-ABCD"} unknown fail',
            $result
        );
    }

    public function testProcessMessageFailureFromDownload()
    {
        $user = new User('pid', 'type');
        $user->setId(1);
        $item = new QueueEntity();
        $item->setId(99);
        $item->setOptions('{"reference":"OLCS-1234-ABCD", "name": "FILENAME"}');
        $item->setCreatedBy($user);

        $expectedQryData = ['reference' => 'OLCS-1234-ABCD'];
        $qryResult = [
            'completed' => true,
            'token' => 'secrettoken',
            'extension' => 'csv',
        ];
        $this->expectQuery(
            \Dvsa\Olcs\Transfer\Query\Cpms\ReportStatus::class,
            $expectedQryData,
            $qryResult
        );

        $expectedCmdData = [
            'reference' => 'OLCS-1234-ABCD',
            'token'     => 'secrettoken',
            'filename'  => 'FILENAME.csv',
            'user'      => 1
        ];
        $this->expectCommandException(
            \Dvsa\Olcs\Transfer\Command\Cpms\DownloadReport::class,
            $expectedCmdData,
            \Dvsa\Olcs\Api\Domain\Exception\Exception::class,
            'backend fail'
        );

        $this->expectCommand(
            \Dvsa\Olcs\Api\Domain\Command\Queue\Failed::class,
            ['item' => $item],
            new Result()
        );

        $result = $this->sut->processMessage($item);

        $this->assertEquals(
            'Failed to process message: 99 {"reference":"OLCS-1234-ABCD", "name": "FILENAME"} backend fail',
            $result
        );
    }
}
