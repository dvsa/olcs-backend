<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication\UpdateFull as CreateHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\UpdateFull as CreateCmd;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\UpdateCountries;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\UpdateMultipleNoOfPermits;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\SubmitApplicationPath;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Update Irhp Application test
 */
class UpdateFullTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateHandler();
        $this->mockRepo('IrhpApplication', IrhpApplicationRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $permitTypeId = 1;
        $licenceId = 2;

        $cmdData = [
            'id' => 4,
            'type' => $permitTypeId,
            'licence' => $licenceId,
            'dateReceived' => '2019-01-03',
            'declaration' => 0,
            'countries' => ['DE', 'FR', 'NL'],
            'permitsRequired' => [
                'DE' => [
                    2019 => 2,
                    2020 => 2
                ],
                'FR' => [
                    2019 => 2,
                    2020 => 2
                ],
                'NL' => [
                    2020 => 2
                ],
            ]
        ];

        $command = CreateCmd::create($cmdData);

        $irhpApplicationEntity = m::mock(IrhpApplication::class);

        $this->repoMap['IrhpApplication']
            ->shouldReceive('fetchById')
            ->with(4)
            ->once()
            ->andReturn($irhpApplicationEntity);

        $irhpApplicationEntity->shouldReceive('getId')
            ->times(3)
            ->andReturn(4);

        $irhpApplicationEntity->shouldReceive('updateDateReceived')
            ->once()
            ->with($cmdData['dateReceived']);

        $irhpApplicationEntity->shouldReceive('resetSectionCompletion')
            ->twice();

        $irhpApplicationEntity->shouldReceive('getIrhpPermitType->getId')
            ->andReturn(4);

        $this->repoMap['IrhpApplication']
            ->shouldReceive('save')
            ->with($irhpApplicationEntity)
            ->once()
            ->andReturn($irhpApplicationEntity);

        $result1 = new Result();
        $result1->addMessage('section updated');
        $sideEffectData = [
            'id' => 4,
            'countries' => array_keys($command->getPermitsRequired())
        ];
        $this->expectedSideEffect(UpdateCountries::class, $sideEffectData, $result1);

        $this->repoMap['IrhpApplication']
            ->shouldReceive('refresh')
            ->twice()
            ->andReturnSelf();

        $result2 = new Result();
        $result2->addMessage('section updated');
        $sideEffectData = [
            'id' => 4,
            'permitsRequired' => $command->getPermitsRequired()
        ];
        $this->expectedSideEffect(UpdateMultipleNoOfPermits::class, $sideEffectData, $result2);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'irhpApplication' => 4,
            ],
            'messages' => [
                0 => 'section updated',
                1 => 'section updated',
                2 => 'IRHP Application updated successfully',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandMultilateral()
    {
        $permitTypeId = 4;
        $licenceId = 2;

        $cmdData = [
            'id' => 44,
            'type' => $permitTypeId,
            'licence' => $licenceId,
            'dateReceived' => '2020-01-03',
            'declaration' => 0,
            'countries' => ['DE', 'FR', 'NL'],
            'permitsRequired' => [
                '2020' => 10,
                '2021' => 12
            ]
        ];

        $command = CreateCmd::create($cmdData);

        $irhpApplicationEntity = m::mock(IrhpApplication::class);

        $this->repoMap['IrhpApplication']
            ->shouldReceive('fetchById')
            ->with(44)
            ->once()
            ->andReturn($irhpApplicationEntity);

        $irhpApplicationEntity->shouldReceive('getId')
            ->times(2)
            ->andReturn(44);

        $irhpApplicationEntity->shouldReceive('updateDateReceived')
            ->once()
            ->with($cmdData['dateReceived']);

        $irhpApplicationEntity->shouldReceive('resetSectionCompletion')
            ->twice();

        $irhpApplicationEntity->shouldReceive('getIrhpPermitType->getId')
            ->andReturn(5);

        $this->repoMap['IrhpApplication']
            ->shouldReceive('save')
            ->with($irhpApplicationEntity)
            ->once()
            ->andReturn($irhpApplicationEntity);



        $this->repoMap['IrhpApplication']
            ->shouldReceive('refresh')
            ->twice()
            ->andReturnSelf();

        $result2 = new Result();
        $result2->addMessage('section updated');
        $sideEffectData = [
            'id' => 44,
            'permitsRequired' => $command->getPermitsRequired()
        ];
        $this->expectedSideEffect(UpdateMultipleNoOfPermits::class, $sideEffectData, $result2);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'irhpApplication' => 44,
            ],
            'messages' => [
                0 => 'section updated',
                1 => 'IRHP Application updated successfully',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandShortTerm()
    {
        $permitTypeId = 2;
        $licenceId = 2;

        $cmdData = [
            'id' => 34,
            'type' => $permitTypeId,
            'licence' => $licenceId,
            'dateReceived' => '2090-01-03',
            'declaration' => 0,
            'postData' => ['key' => 'val']
        ];

        $command = CreateCmd::create($cmdData);

        $irhpApplicationEntity = m::mock(IrhpApplication::class);

        $this->repoMap['IrhpApplication']
            ->shouldReceive('fetchById')
            ->with(34)
            ->once()
            ->andReturn($irhpApplicationEntity);

        $irhpApplicationEntity->shouldReceive('getId')
            ->times(2)
            ->andReturn(34);

        $irhpApplicationEntity->shouldReceive('updateDateReceived')
            ->once()
            ->with($cmdData['dateReceived']);


        $irhpApplicationEntity->shouldReceive('getIrhpPermitType->getId')
            ->andReturn(2);

        $this->repoMap['IrhpApplication']
            ->shouldReceive('save')
            ->with($irhpApplicationEntity)
            ->once()
            ->andReturn($irhpApplicationEntity);


        $result2 = new Result();

        $sideEffectData = [
            'id' => 34,
            'postData' => ['key' => 'val']
        ];
        $this->expectedSideEffect(SubmitApplicationPath::class, $sideEffectData, $result2);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'irhpApplication' => 34,
            ],
            'messages' => [
                0 => 'IRHP Application updated successfully',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
