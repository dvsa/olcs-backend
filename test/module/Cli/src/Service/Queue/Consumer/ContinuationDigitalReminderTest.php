<?php

namespace Dvsa\OlcsTest\Cli\Service\Queue\Consumer;

use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\ContinuationDigitalReminder;
use Interop\Container\ContainerInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Class ContinuationDigitalReminderTest
 * @package Dvsa\OlcsTest\Cli\Service\Queue\Consumer
 */
class ContinuationDigitalReminderTest extends MockeryTestCase
{
    protected $queueEntity = null;

    public function testGetCommandData()
    {
        $ci = m::mock(ContainerInterface::class);
        $sut = new ContinuationDigitalReminder($ci);

        $queue = new Queue();
        $queue->setEntityId(87);
        $queue->setCreatedBy(
            m::mock()->shouldReceive('getId')->with()->once()->andReturn(9)->getMock()
        );

        $this->assertSame(
            [
                'id' => 87,
                'user' => 9,
            ],
            $sut->getCommandData($queue)
        );
    }
}
