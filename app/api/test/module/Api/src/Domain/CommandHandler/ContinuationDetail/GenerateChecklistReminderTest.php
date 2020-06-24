<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\ContinuationDetail;

use Dvsa\Olcs\Api\Domain\Command\ContinuationDetail\GenerateChecklistDocument;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Doc\Document;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\ContinuationDetail\GenerateChecklistReminder as CommandHandler;
use Dvsa\Olcs\Api\Domain\Command\ContinuationDetail\GenerateCheckListReminder as Command;
use Dvsa\Olcs\Api\Domain\Repository;
use Mockery as m;

/**
 * Class GenerateChecklistReminderTest
 */
class GenerateChecklistReminderTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('ContinuationDetail', Repository\ContinuationDetail::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->references = [
            Document::class => [
                76 => m::mock(Document::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $continuationDetail = new ContinuationDetail();
        $continuationDetail->setId(87);
        $continuationDetail->setDigitalReminderSent(false);

        $this->repoMap['ContinuationDetail']
            ->shouldReceive('fetchById')->with(87)->once()->andReturn($continuationDetail)
            ->shouldReceive('save')->with($continuationDetail)->once();

        $this->expectedSideEffect(
            GenerateChecklistDocument::class,
            [
                'id' => 87,
                'user' => 4,
                'enforcePrint' => true,
            ],
            (new Result())->addId('document', 76)
        );

        $command = Command::create(['id' => 87, 'user' => 4]);
        $result = $this->sut->handleCommand($command);

        $this->assertEquals(true, $continuationDetail->getDigitalReminderSent());
        $this->assertEquals($this->references[Document::class][76], $continuationDetail->getChecklistDocument());
        $this->assertEquals(['Reminder sent'], $result->getMessages());
    }

    public function testHandleCommandAlreadySent()
    {
        $continuationDetail = new ContinuationDetail();
        $continuationDetail->setDigitalReminderSent(true);

        $this->repoMap['ContinuationDetail']
            ->shouldReceive('fetchById')->with(87)->once()->andReturn($continuationDetail);

        $command = Command::create(['id' => 87]);
        $result = $this->sut->handleCommand($command);
        $this->assertEquals(['Reminder has already been sent'], $result->getMessages());
    }
}
