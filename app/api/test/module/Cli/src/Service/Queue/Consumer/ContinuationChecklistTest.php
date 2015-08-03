<?php

/**
 * Continuation Checklist Queue Consumer Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Cli\Service\Queue\Consumer;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\ContinuationChecklist as Sut;
use Dvsa\OlcsTest\Cli\Service\Queue\Consumer\AbstractConsumerTestCase;

/**
 * Continuation Checklist Queue Consumer Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ContinuationChecklistTest extends AbstractConsumerTestCase
{
    protected $consumerClass = Sut::class;

    public function testProcessMessageSuccess()
    {
        $item = new QueueEntity();
        $item->setId(99);
        $item->setEntityId(69);

        $expectedDtoData = ['id' => 69];
        $cmdResult = new Result();
        $cmdResult
            ->addId('continuationDetail', 69)
            ->addId('document', 101)
            ->addMessage('ContinuationDetail updated')
            ->addMessage('Document created');

        $this->expectCommand(
            \Dvsa\Olcs\Api\Domain\Command\ContinuationDetail\Process::class,
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
            'Successfully processed message: 99  ContinuationDetail updated, Document created',
            $result
        );
    }
}
