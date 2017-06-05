<?php

namespace Dvsa\OlcsTest\Cli\Service\Queue\Consumer;

use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\RemoveDeleteDocuments;
use Mockery as m;

/**
 * @covers \Dvsa\Olcs\Cli\Service\Queue\Consumer\RemoveDeleteDocuments
 */
class RemoveDeletedDocumentsTest extends AbstractConsumerTestCase
{
    protected $consumerClass = RemoveDeleteDocuments::class;

    /** @var  RemoveDeleteDocuments */
    protected $sut;

    public function testGetCommandData()
    {
        $mockQueue = m::mock(Queue::class);
        $this->assertSame([], $this->sut->getCommandData($mockQueue));
    }
}
