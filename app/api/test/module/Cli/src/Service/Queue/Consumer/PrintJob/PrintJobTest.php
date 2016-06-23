<?php

namespace Dvsa\OlcsTest\Cli\Service\Queue\Consumer\PrintJob;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\PrintJob\PrintJob as Sut;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\PrintJob as PrintJobCmd;
use Dvsa\Olcs\Api\Domain\Command\Queue\Retry as RetryCmd;
use Dvsa\Olcs\Api\Domain\Exception\NotReadyException;
use Dvsa\OlcsTest\Cli\Service\Queue\Consumer\AbstractConsumerTestCase;
use Dvsa\Olcs\Api\Entity\User\User;
use Zend\Serializer\Adapter\Json as ZendJson;

/**
 * Print job test
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PrintJobTest extends AbstractConsumerTestCase
{
    protected $consumerClass = Sut::class;

    /**
     * Tests that print job retries correctly
     */
    public function testProcessMessageHandlesNotReadyException()
    {
        $message = 'exception message';
        $userId = 11;
        $user = new User('pid', 'type');
        $user->setId($userId);

        $itemId = 99;
        $entityId = 88;
        $jobName = 'job name';

        $optionsArray = [
            'jobName' => $jobName,
            'userId' => $userId
        ];

        $json = new ZendJson();
        $options = $json->serialize($optionsArray);

        $item = new QueueEntity();
        $item->setId($itemId);
        $item->setEntityId($entityId);
        $item->setOptions($options);
        $item->setCreatedBy($user);

        $retryAfter = 900;

        $cmdData = [
            'id' => $itemId,
            'title' => $jobName,
            'document' => $entityId,
            'user' => $userId
        ];

        $this->expectCommandException(PrintJobCmd::class, $cmdData, NotReadyException::class, $message, $retryAfter);

        $this->expectCommand(
            RetryCmd::class,
            [
                'item' => $item,
                'retryAfter' => $retryAfter
            ],
            new Result()
        );

        $result = $this->sut->processMessage($item);

        $this->assertEquals(
            'Requeued message: 99 ' . $options . ' for retry in ' . $retryAfter . ' ' . $message,
            $result
        );
    }
}
