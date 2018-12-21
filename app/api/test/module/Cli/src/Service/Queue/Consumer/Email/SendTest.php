<?php

namespace Dvsa\OlcsTest\Cli\Service\Queue\Consumer\Email;

use Dvsa\Olcs\Api\Domain\Command\Email\SendUserRegistered as SampleEmail;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\Email\Send;
use Dvsa\Olcs\Email\Exception\EmailNotSentException;
use Dvsa\OlcsTest\Cli\Service\Queue\Consumer\AbstractConsumerTestCase;
use Zend\Serializer\Adapter\Json as ZendJson;
use Zend\ServiceManager\Exception\RuntimeException as ZendServiceException;

/**
 * @covers \Dvsa\Olcs\Cli\Service\Queue\Consumer\Email\Send
 * @covers \Dvsa\Olcs\Cli\Service\Queue\Consumer\AbstractCommandConsumer
 */
class SendTest extends AbstractConsumerTestCase
{
    protected $consumerClass = Send::class;

    /** @var  Send */
    protected $sut;

    public function testProcessMessageSuccess()
    {
        $json = new ZendJson();
        $options = $json->serialize(
            [
                'commandClass' => SampleEmail::class,
                'commandData' => [
                    'user' => 1,
                ]
            ]
        );

        $item = new QueueEntity();
        $item->setId(99);
        $item->setOptions($options);

        $expectedDtoData = ['user' => 1];
        $cmdResult = new Result();
        $cmdResult
            ->addMessage('Email sent');

        $this->expectCommand(
            SampleEmail::class,
            $expectedDtoData,
            $cmdResult
        );

        $this->expectCommand(
            \Dvsa\Olcs\Api\Domain\Command\Queue\Complete::class,
            ['item' => $item],
            new Result(),
            false
        );

        $result = $this->sut->processMessage($item);

        $this->assertEquals(
            'Successfully processed message: 99 ' . $options . ' Email sent',
            $result
        );
    }

    public function testProcessMessageHandlesEmailNotSentException()
    {
        $json = new ZendJson();
        $options = $json->serialize(
            [
                'commandClass' => SampleEmail::class,
                'commandData' => [
                    'user' => 1,
                ]
            ]
        );

        $item = new QueueEntity();
        $item->setId(99);
        $item->setOptions($options);

        $message = 'Email not sent';

        $this->chm
            ->shouldReceive('handleCommand')
            ->with(SampleEmail::class)
            ->andThrow(new EmailNotSentException($message));

        $this->expectCommand(
            \Dvsa\Olcs\Api\Domain\Command\Queue\Retry::class,
            [
                'item' => $item,
                'retryAfter' => 900,
                'lastError' => $message,
            ],
            new Result(),
            false
        );

        $result = $this->sut->processMessage($item);

        $this->assertEquals(
            'Requeued message: 99 ' . $options . ' for retry in 900 ' . $message,
            $result
        );
    }

    public function testProcessMessageHandlesZendServiceException()
    {
        $json = new ZendJson();
        $options = $json->serialize(
            [
                'commandClass' => SampleEmail::class,
                'commandData' => [
                    'user' => 1,
                ]
            ]
        );

        $item = new QueueEntity();
        $item->setId(99);
        $item->setOptions($options);

        $message = 'Email not sent';

        $this->chm
            ->shouldReceive('handleCommand')
            ->with(SampleEmail::class)
            ->andThrow(new ZendServiceException($message));

        $this->expectCommand(
            \Dvsa\Olcs\Api\Domain\Command\Queue\Failed::class,
            [
                'item' => $item,
                'lastError' => 'Email not sent',
            ],
            new Result(),
            false
        );

        $result = $this->sut->processMessage($item);

        $this->assertEquals(
            'Failed to process message: 99 ' . $options . ' ' . $message,
            $result
        );
    }

    public function testProcessMessageHandlesException()
    {
        $json = new ZendJson();
        $options = $json->serialize(
            [
                'commandClass' => SampleEmail::class,
                'commandData' => [
                    'user' => 1,
                ]
            ]
        );

        $item = new QueueEntity();
        $item->setId(99);
        $item->setOptions($options);

        $message = 'Email not sent';

        $this->chm
            ->shouldReceive('handleCommand')
            ->with(SampleEmail::class)
            ->andThrow(new \Exception($message));

        $this->expectCommand(
            \Dvsa\Olcs\Api\Domain\Command\Queue\Failed::class,
            [
                'item' => $item,
                'lastError' => 'Email not sent',
            ],
            new Result(),
            false
        );

        $result = $this->sut->processMessage($item);

        $this->assertEquals(
            'Failed to process message: 99 ' . $options . ' ' . $message,
            $result
        );
    }

    public function testProcessMessageHandlesMaxAttempts()
    {
        $json = new ZendJson();
        $options = $json->serialize(
            [
                'commandClass' => SampleEmail::class,
                'commandData' => [
                    'user' => 1,
                ]
            ]
        );

        $item = new QueueEntity();
        $item->setId(99);
        $item->setAttempts(100);
        $item->setOptions($options);

        $this->chm
            ->shouldReceive('handleCommand')
            ->never();

        $this->expectCommand(
            \Dvsa\Olcs\Api\Domain\Command\Queue\Failed::class,
            [
                'item' => $item,
                'lastError' => QueueEntity::ERR_MAX_ATTEMPTS,
            ],
            new Result(),
            false
        );

        $result = $this->sut->processMessage($item);

        $this->assertEquals(
            'Failed to process message: 99 ' . $options . ' ' . QueueEntity::ERR_MAX_ATTEMPTS,
            $result
        );
    }
}
