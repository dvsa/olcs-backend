<?php

namespace Dvsa\OlcsTest\Cli\Service\Queue\Consumer;

use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\ContinuationDigitalReminder;
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
        $sut = new ContinuationDigitalReminder();

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
