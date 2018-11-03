<?php

namespace Dvsa\OlcsTest\Cli\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Permits\GeneratePermitDocuments;
use Dvsa\Olcs\Api\Domain\Command\Permits\ProceedToStatus;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit;
use Dvsa\Olcs\Cli\Domain\Command\Permits\GeneratePermits;
use Dvsa\Olcs\Cli\Domain\CommandHandler\Permits\GeneratePermits as GeneratePermitsHandler;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

/**
 * Generate Permits Test
 */
class GeneratePermitsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new GeneratePermitsHandler();

        parent::setUp();
    }

    /**
     * @expectedException Dvsa\Olcs\Api\Domain\Exception\ValidationException
     * @expectedExceptionMessage Empty list of permits provided.
     */
    public function testHandleWithoutIds()
    {
        $cmdData = [
            'ids' => [],
            'user' => 456,
        ];

        $command = GeneratePermits::create($cmdData);

        $this->sut->handleCommand($command);
    }

    /**
     * @expectedException Dvsa\Olcs\Api\Domain\Exception\ValidationException
     * @expectedExceptionMessage No user provided.
     */
    public function testHandleWithoutUser()
    {
        $cmdData = [
            'ids' => [1, 2, 3],
            'user' => null,
        ];

        $command = GeneratePermits::create($cmdData);

        $this->sut->handleCommand($command);
    }

    /**
     * @dataProvider dpHandleButIssueWithDocs
     */
    public function testHandleButIssueWithDocs($permitDocs, $letterDocs, $expected)
    {
        $ids = [1, 2, 3];
        $userId = 456;

        $cmdData = [
            'ids' => $ids,
            'user' => $userId,
        ];

        $command = GeneratePermits::create($cmdData);

        $this->mockTransationMngr
            ->shouldReceive('beginTransaction')
            ->times(3)
            ->shouldReceive('commit')
            ->times(2)
            ->shouldReceive('rollback')
            ->once();

        $this->expectedSideEffect(
            ProceedToStatus::class,
            [
                'ids' => $ids,
                'status' => IrhpPermit::STATUS_PRINTING,
            ],
            (new Result())->addMessage('Permits proceeded to Printing')
        );

        $docsResult = new Result();
        if (isset($permitDocs)) {
            $docsResult->addId('permit', $permitDocs);
        }
        if (isset($letterDocs)) {
            $docsResult->addId('coveringLetter', $letterDocs);
        }
        $docsResult->addMessage('Docs generated');

        $this->expectedSideEffect(
            GeneratePermitDocuments::class,
            [
                'ids' => $ids,
            ],
            $docsResult
        );

        $this->expectedSideEffect(
            ProceedToStatus::class,
            [
                'ids' => $ids,
                'status' => IrhpPermit::STATUS_ERROR,
            ],
            (new Result())->addMessage('Permits proceeded to Error')
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($expected);

        $this->sut->handleCommand($command);
    }

    public function dpHandleButIssueWithDocs()
    {
        return [
            'no docs generated' => [
                null,
                null,
                'Permits generation failed with error: No documents generated.'
            ],
            'no permit docs generated' => [
                [],
                [201, 202, 203],
                'Permits generation failed with error: No permits generated.'
            ],
            'no letter docs generated' => [
                [101, 102, 103],
                [],
                'Permits generation failed with error: No covering letters generated.'
            ],
            'different number permits and letters generated' => [
                [101, 102, 103],
                [201, 202],
                'Permits generation failed with error: Number of permits (3) and letters (2) does not match.'
            ],
        ];
    }

    /**
     * @expectedException Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     * @expectedExceptionMessage Permits generation failed with error: Letter printing failed.
     */
    public function testHandleButIssueWithLetterPrinting()
    {
        $ids = [1, 2, 3];
        $userId = 456;
        $permitDocs = [101, 102, 103];
        $letterDocs = [201, 202, 203];

        $cmdData = [
            'ids' => $ids,
            'user' => $userId,
        ];

        $command = GeneratePermits::create($cmdData);

        $this->mockTransationMngr
            ->shouldReceive('beginTransaction')
            ->times(3)
            ->shouldReceive('commit')
            ->times(2)
            ->shouldReceive('rollback')
            ->once();

        $this->expectedSideEffect(
            ProceedToStatus::class,
            [
                'ids' => $ids,
                'status' => IrhpPermit::STATUS_PRINTING,
            ],
            (new Result())->addMessage('Permits proceeded to Printing')
        );

        $this->expectedSideEffect(
            GeneratePermitDocuments::class,
            [
                'ids' => $ids,
            ],
            (new Result())
                ->addId('permit', $permitDocs)
                ->addId('coveringLetter', $letterDocs)
                ->addMessage('Docs generated')
        );

        $this->expectedSideEffectThrowsException(
            Enqueue::class,
            [
                'documentId' => $letterDocs[0],
                'jobName' => 'Permit covering letter',
                'user' => $userId,
            ],
            new RuntimeException('Letter printing failed.')
        );

        $this->expectedSideEffect(
            ProceedToStatus::class,
            [
                'ids' => $ids,
                'status' => IrhpPermit::STATUS_ERROR,
            ],
            (new Result())->addMessage('Permits proceeded to Error')
        );

        $this->sut->handleCommand($command);
    }

    public function testHandleCommand()
    {
        $ids = [1, 2, 3];
        $userId = 456;
        $permitDocs = [101, 102, 103];
        $letterDocs = [201, 202, 203];

        $cmdData = [
            'ids' => $ids,
            'user' => $userId,
        ];

        $command = GeneratePermits::create($cmdData);

        $this->mockTransationMngr
            ->shouldReceive('beginTransaction')
            ->times(2)
            ->shouldReceive('commit')
            ->times(2)
            ->shouldReceive('rollback')
            ->never();

        $this->expectedSideEffect(
            ProceedToStatus::class,
            [
                'ids' => $ids,
                'status' => IrhpPermit::STATUS_PRINTING,
            ],
            (new Result())->addMessage('Permits proceeded to Printing')
        );

        $this->expectedSideEffect(
            GeneratePermitDocuments::class,
            [
                'ids' => $ids,
            ],
            (new Result())
                ->addId('permit', $permitDocs)
                ->addId('coveringLetter', $letterDocs)
                ->addMessage('Docs generated')
        );

        $this->expectedSideEffect(
            Enqueue::class,
            [
                'documentId' => $letterDocs[0],
                'jobName' => 'Permit covering letter',
                'user' => $userId,
            ],
            (new Result())->addMessage(sprintf('Letter %d scheduled for printing', $letterDocs[0]))
        );
        $this->expectedSideEffect(
            Enqueue::class,
            [
                'documentId' => $letterDocs[1],
                'jobName' => 'Permit covering letter',
                'user' => $userId,
            ],
            (new Result())->addMessage(sprintf('Letter %d scheduled for printing', $letterDocs[1]))
        );
        $this->expectedSideEffect(
            Enqueue::class,
            [
                'documentId' => $letterDocs[2],
                'jobName' => 'Permit covering letter',
                'user' => $userId,
            ],
            (new Result())->addMessage(sprintf('Letter %d scheduled for printing', $letterDocs[2]))
        );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'permit' => $permitDocs,
                'coveringLetter' => $letterDocs,
            ],
            'messages' => [
                'Permits proceeded to Printing',
                'Docs generated',
                sprintf('Letter %d scheduled for printing', $letterDocs[0]),
                sprintf('Letter %d scheduled for printing', $letterDocs[1]),
                sprintf('Letter %d scheduled for printing', $letterDocs[2]),
                'Permits generated',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWithOneItemList()
    {
        $ids = [1];
        $userId = 456;
        $permitDocs = 101;
        $letterDocs = 201000;

        $cmdData = [
            'ids' => $ids,
            'user' => $userId,
        ];

        $command = GeneratePermits::create($cmdData);

        $this->mockTransationMngr
            ->shouldReceive('beginTransaction')
            ->times(2)
            ->shouldReceive('commit')
            ->times(2)
            ->shouldReceive('rollback')
            ->never();

        $this->expectedSideEffect(
            ProceedToStatus::class,
            [
                'ids' => $ids,
                'status' => IrhpPermit::STATUS_PRINTING,
            ],
            (new Result())->addMessage('Permits proceeded to Printing')
        );

        $this->expectedSideEffect(
            GeneratePermitDocuments::class,
            [
                'ids' => $ids,
            ],
            (new Result())
                ->addId('permit', $permitDocs)
                ->addId('coveringLetter', $letterDocs)
                ->addMessage('Docs generated')
        );

        $this->expectedSideEffect(
            Enqueue::class,
            [
                'documentId' => $letterDocs,
                'jobName' => 'Permit covering letter',
                'user' => $userId,
            ],
            (new Result())->addMessage(sprintf('Letter %d scheduled for printing', $letterDocs))
        );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'permit' => $permitDocs,
                'coveringLetter' => $letterDocs,
            ],
            'messages' => [
                'Permits proceeded to Printing',
                'Docs generated',
                sprintf('Letter %d scheduled for printing', $letterDocs),
                'Permits generated',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
