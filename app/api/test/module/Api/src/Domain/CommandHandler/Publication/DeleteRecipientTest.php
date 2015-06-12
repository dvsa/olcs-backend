<?php

/**
 * DeleteRecipientTest.php
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Publication;

use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Recipient as RecipientRepo;
use Dvsa\Olcs\Api\Domain\CommandHandler\Publication\DeleteRecipient;
use Dvsa\Olcs\Api\Entity\Publication\Recipient;
use Dvsa\Olcs\Transfer\Command\Publication\DeleteRecipient as Cmd;

/**
 * Class DeleteRecipientTest
 */
class DeleteRecipientTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new DeleteRecipient();
        $this->mockRepo('Recipient', RecipientRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 1
        ];

        $command = Cmd::create($data);

        $recipientEntity = m::mock(Recipient::class)->makePartial();
        $recipientEntity->setId(1);

        $this->repoMap['Recipient']
            ->shouldReceive('fetchUsingId')
            ->once()
            ->andReturn($recipientEntity)
            ->shouldReceive('delete')
            ->once();

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'recipient' => 1
            ],
            'messages' => [
                'Recipient deleted successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
