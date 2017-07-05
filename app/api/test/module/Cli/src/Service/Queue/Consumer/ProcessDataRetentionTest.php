<?php

namespace Dvsa\OlcsTest\Cli\Service\Queue\Consumer;

use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\ProcessDataRetention;
use Mockery as m;

/**
 * @covers \Dvsa\Olcs\Cli\Service\Queue\Consumer\ProcessDataRetention
 */
class ProcessDataRetentionTest extends AbstractConsumerTestCase
{
    protected $consumerClass = ProcessDataRetention::class;

    /** @var  ProcessDataRetention */
    protected $sut;

    public function testGetCommandData()
    {
        $mockQueue = m::mock(Queue::class);
        $this->assertSame([], $this->sut->getCommandData($mockQueue));
    }
}
