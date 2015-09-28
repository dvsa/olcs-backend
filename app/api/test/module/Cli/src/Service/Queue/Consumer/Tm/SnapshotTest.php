<?php

/**
 * Snapshot Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Cli\Service\Queue\Consumer\Tm;

use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\Tm\Snapshot as Sut;
use Dvsa\OlcsTest\Cli\Service\Queue\Consumer\AbstractConsumerTestCase;

/**
 * Snapshot Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class SnapshotTest extends AbstractConsumerTestCase
{
    protected $consumerClass = Sut::class;

    public function testGetCommandData()
    {
        $item = new QueueEntity();
        $item->setEntityId(111);

        $result = $this->sut->getCommandData($item);

        $this->assertEquals(['id' => 111], $result);
    }
}
