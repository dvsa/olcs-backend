<?php

namespace Dvsa\OlcsTest\Cli\Service\Queue\Consumer\Tm;

use Dvsa\Olcs\Api\Domain\Exception\NysiisException;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\Tm\UpdateTmNysiisName as Sut;
use Dvsa\OlcsTest\Cli\Service\Queue\Consumer\AbstractConsumerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Tm\UpdateNysiisName as UpdateNysiisNameCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Queue\Retry as RetryCmd;
use Zend\Serializer\Adapter\Json as ZendJson;
use Zend\ServiceManager\Exception\ExceptionInterface as ZendServiceException;

/**
 * Update Tm Nysiis name Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class UpdateTmNysiisNameTest extends AbstractConsumerTestCase
{
    protected $consumerClass = Sut::class;

    public function testGetCommandData()
    {
        $item = new QueueEntity();
        $item->setEntityId(111);
        $item->setOptions(json_encode(['foo' => 'bar']));

        $result = $this->sut->getCommandData($item);

        $this->assertEquals(['id' => 111, 'foo' => 'bar'], $result);
    }

    /**
     * Tests that Nysiis exceptions are caught and retried correctly
     */
    public function testProcessMessageHandlesNysiisException()
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
            'id' => $itemId
        ];

        $this->expectCommandException(
            UpdateNysiisNameCmd::class, $cmdData, NysiisException::class, $message, $retryAfter
        );

        $this->expectCommand(
            RetryCmd::class,
            [
                'item' => $item,
                'retryAfter' => $retryAfter
            ],
            new Result(),
            false
        );

        $result = $this->sut->processMessage($item);

        $this->assertEquals(
            'Requeued message: 99 ' . $options . ' for retry in ' . $retryAfter . ' ' . $message,
            $result
        );
    }

    /**
     * Tests that print job retries correctly
     */
    public function testProcessMessageHandlesZendServiceException()
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
            'id' => $itemId
        ];

        $this->expectCommandException(
            UpdateNysiisNameCmd::class, $cmdData, ZendServiceException::class, $message, $retryAfter
        );

        $this->expectCommand(
            RetryCmd::class,
            [
                'item' => $item,
                'retryAfter' => $retryAfter
            ],
            new Result(),
            false
        );

        $result = $this->sut->processMessage($item);

        $this->assertEquals(
            'Requeued message: 99 ' . $options . ' for retry in ' . $retryAfter . ' ' . $message,
            $result
        );
    }
}
