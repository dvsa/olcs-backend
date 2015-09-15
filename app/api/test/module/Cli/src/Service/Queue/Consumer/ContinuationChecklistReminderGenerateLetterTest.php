<?php

/**
 * Continuation Checklist Reminder Generate Letter Queue Consumer Test
 *
 * @author Alex Peshkov <alex.oeshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Cli\Service\Queue\Consumer;

use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\ContinuationChecklistReminderGenerateLetter as Sut;

/**
 * Continuation Checklist Reminder Generate Letter Queue Consumer Test
 *
 * @author Alex Peshkov <alex.oeshkov@valtech.co.uk>
 */
class ContinuationChecklistReminderGenerateLetterTest extends AbstractConsumerTestCase
{
    protected $consumerClass = Sut::class;

    public function testGetCommandData()
    {
        $item = new QueueEntity();
        $item->setEntityId(69);

        $result = $this->sut->getCommandData($item);

        $this->assertEquals(
            ['id' => 69],
            $result
        );
    }
}
