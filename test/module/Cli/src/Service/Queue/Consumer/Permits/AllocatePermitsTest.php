<?php

/**
 * Allocate Permits Test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
namespace Dvsa\OlcsTest\Cli\Service\Queue\Consumer\Permits;

use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\Permits\AllocatePermits as Sut;
use Dvsa\OlcsTest\Cli\Service\Queue\Consumer\AbstractConsumerTestCase;

/**
 * Allocate Permits Test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class AllocatePermitsTest extends AbstractConsumerTestCase
{
    protected $consumerClass = Sut::class;

    public function testGetCommandData()
    {
        $item = new QueueEntity();
        $item->setEntityId(244);

        $result = $this->sut->getCommandData($item);

        $this->assertEquals(['id' => 244], $result);
    }
}
