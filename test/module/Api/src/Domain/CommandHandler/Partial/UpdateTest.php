<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Partial;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\PartialMarkup\Update;
use Dvsa\Olcs\Api\Domain\Command\PartialMarkup\Create;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Partial\Update as UpdateHandler;
use Dvsa\Olcs\Api\Domain\Repository\Partial as PartialRepo;
use Dvsa\Olcs\Api\Domain\Repository\PartialMarkup as PartialMarkupRepo;
use Dvsa\Olcs\Api\Domain\Repository\Language as LanguageRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\PartialMarkup\Update as UpdateCmd;
use Dvsa\Olcs\Api\Entity\System\Partial as PartialEntity;
use Dvsa\Olcs\Api\Entity\System\PartialMarkup as PartialMarkupEntity;

/**
 * Update Partial Test
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
class UpdateTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new UpdateHandler();
        $this->mockRepo('Partial', PartialRepo::class);
        $this->mockRepo('PartialMarkup', PartialMarkupRepo::class);
        $this->mockRepo('Language', LanguageRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $id = 'TEST_STR_ID';
        $translationsArray = [
            'en_GB' => base64_encode('English'),
            'cy_GB' => base64_encode('Welsh'),
            'en_NI' => base64_encode('English (NI)'),
            'cy_NI' => base64_encode('Welsh (NI)')
        ];

        $cmdData = [
            'id' => $id,
            'translationsArray' => $translationsArray
        ];

        $command = UpdateCmd::create($cmdData);

        $entity = m::mock(PartialEntity::class);

        $tktEntity = m::mock(PartialMarkupEntity::class);

        $this->repoMap['Partial']
            ->shouldReceive('fetchUsingId')
            ->with($command)
            ->once()
            ->andReturn($entity);

        $this->repoMap['PartialMarkup']
            ->shouldReceive('fetchByParentLanguage')
            ->with($id, 2)
            ->once()
            ->andReturn(null);

        $this->repoMap['PartialMarkup']
            ->shouldReceive('fetchByParentLanguage')
            ->with($id, 3)
            ->once()
            ->andReturn(null);

        $this->repoMap['PartialMarkup']
            ->shouldReceive('fetchByParentLanguage')
            ->with($id, 4)
            ->once()
            ->andReturn(null);

        $this->repoMap['PartialMarkup']
            ->shouldReceive('fetchByParentLanguage')
            ->with($id, 1)
            ->once()
            ->andReturn($tktEntity);

        $entity->shouldReceive('getId')
            ->withNoArgs()
            ->times(5)
            ->andReturn($id);

        $tktEntity
            ->shouldReceive('getId')
            ->once()
            ->withNoArgs()
            ->andReturn(22);

        $this->expectedSideEffect(
            Create::class,
            [
                'partial' => $id,
                'language' => 2,
                'markup' => base64_decode($translationsArray['cy_GB'])
            ],
            new Result(),
            1
        );

        $this->expectedSideEffect(
            Update::class,
            [
                'id' => 22,
                'markup' => base64_decode($translationsArray['en_GB'])
            ],
            new Result(),
            1
        );

        $this->expectedSideEffect(
            Create::class,
            [
                'partial' => $id,
                'language' => 3,
                'markup' => base64_decode($translationsArray['en_NI'])
            ],
            new Result(),
            1
        );

        $this->expectedSideEffect(
            Create::class,
            [
                'partial' => $id,
                'language' => 4,
                'markup' => base64_decode($translationsArray['cy_NI'])
            ],
            new Result(),
            1
        );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'Partial' => 'TEST_STR_ID'
            ],
            'messages' => [
                'Translations Updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandBadLanguage()
    {
        $id = 'TEST_STR_ID';
        $translationsArray = [
            'ERROR' => 'English'
        ];

        $cmdData = [
            'id' => $id,
            'translationsArray' => $translationsArray
        ];

        $entity = m::mock(PartialEntity::class);

        $command = UpdateCmd::create($cmdData);

        $this->repoMap['Partial']
            ->shouldReceive('fetchUsingId')
            ->with($command)
            ->once()
            ->andReturn($entity);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Error processing translation. Invalid or unsupported language code');

        $this->sut->handleCommand($command);
    }
}
