<?php

/**
 * ECMT Post Submission Task Queue Consumer Test
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
namespace Dvsa\OlcsTest\Cli\Service\Queue\Consumer\Permits;

use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\Permits\EcmtPostSubmitTasks as Sut;
use Dvsa\OlcsTest\Cli\Service\Queue\Consumer\AbstractConsumerTestCase;

class EcmtPostSubmitTasksTest extends AbstractConsumerTestCase
{
    protected $consumerClass = Sut::class;

    public function testGetCommandData()
    {
        $item = new QueueEntity();
        $item->setEntityId(187);

        $result = $this->sut->getCommandData($item);

        $this->assertEquals(['id' => 187], $result);
    }
}
