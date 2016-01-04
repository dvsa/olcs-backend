<?php

/**
 * Email Send Queue Consumer Test
 */
namespace Dvsa\OlcsTest\Cli\Service\Queue\Consumer\Email;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\Email\Send as Sut;
use Dvsa\OlcsTest\Cli\Service\Queue\Consumer\AbstractConsumerTestCase;
use Zend\Serializer\Adapter\Json as ZendJson;

/**
 * Email Send Queue Consumer Test
 */
class SendTest extends AbstractConsumerTestCase
{
    protected $consumerClass = Sut::class;

    public function testProcessMessageSuccess()
    {
        $json = new ZendJson();
        $options = $json->serialize(
            [
                'commandClass' => \Dvsa\Olcs\Api\Domain\Command\Email\SendUserRegistered::class,
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
            \Dvsa\Olcs\Api\Domain\Command\Email\SendUserRegistered::class,
            $expectedDtoData,
            $cmdResult
        );

        $this->expectCommand(
            \Dvsa\Olcs\Api\Domain\Command\Queue\Complete::class,
            ['item' => $item],
            new Result()
        );

        $result = $this->sut->processMessage($item);

        $this->assertEquals(
            'Successfully processed message: 99 ' . $options . ' Email sent',
            $result
        );
    }
}
