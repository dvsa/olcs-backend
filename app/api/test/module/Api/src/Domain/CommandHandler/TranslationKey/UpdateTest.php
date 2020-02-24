<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\TranslationKey;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\TranslationKeyText\Update;
use Dvsa\Olcs\Api\Domain\Command\TranslationKeyText\Create;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\TranslationKey\Update as UpdateHandler;
use Dvsa\Olcs\Api\Domain\Repository\TranslationKey as TranslationKeyRepo;
use Dvsa\Olcs\Api\Domain\Repository\TranslationKeyText as TranslationKeyTextRepo;
use Dvsa\Olcs\Api\Domain\Repository\Language as LanguageRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\TranslationKey\Update as UpdateCmd;
use Dvsa\Olcs\Api\Entity\System\TranslationKey as TranslationKeyEntity;
use Dvsa\Olcs\Api\Entity\System\TranslationKeyText as TranslationKeyTextEntity;

/**
 * Update TranslationKey Test
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
class UpdateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateHandler();
        $this->mockRepo('TranslationKey', TranslationKeyRepo::class);
        $this->mockRepo('TranslationKeyText', TranslationKeyTextRepo::class);
        $this->mockRepo('Language', LanguageRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $id = 'TEST_STR_ID';
        $translationsArray = [
            'en_GB' => 'English',
            'cy_GB' => 'Welsh',
            'en_NI' => 'English (NI)',
            'cy_NI' => 'Welsh (NI)'
        ];

        $cmdData = [
            'id' => $id,
            'translationsArray' => $translationsArray
        ];

        $command = UpdateCmd::create($cmdData);

        $entity = m::mock(TranslationKeyEntity::class);

        $tktEntity = m::mock(TranslationKeyTextEntity::class);

        $this->repoMap['TranslationKey']
            ->shouldReceive('fetchUsingId')
            ->with($command)
            ->once()
            ->andReturn($entity);

        $this->repoMap['TranslationKeyText']
            ->shouldReceive('fetchByTranslationKeyLanguage')
            ->with($id, 2)
            ->once()
            ->andReturn(null);

        $this->repoMap['TranslationKeyText']
            ->shouldReceive('fetchByTranslationKeyLanguage')
            ->with($id, 3)
            ->once()
            ->andReturn(null);

        $this->repoMap['TranslationKeyText']
            ->shouldReceive('fetchByTranslationKeyLanguage')
            ->with($id, 4)
            ->once()
            ->andReturn(null);

        $this->repoMap['TranslationKeyText']
            ->shouldReceive('fetchByTranslationKeyLanguage')
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
                'translationKey' => $id,
                'language' => 2,
                'translatedText' => $translationsArray['cy_GB']
            ],
            new Result(),
            1
        );

        $this->expectedSideEffect(
            Update::class,
            [
                'id' => 22,
                'translatedText' => $translationsArray['en_GB']
            ],
            new Result(),
            1
        );

        $this->expectedSideEffect(
            Create::class,
            [
                'translationKey' => $id,
                'language' => 3,
                'translatedText' => $translationsArray['en_NI']
            ],
            new Result(),
            1
        );

        $this->expectedSideEffect(
            Create::class,
            [
                'translationKey' => $id,
                'language' => 4,
                'translatedText' => $translationsArray['cy_NI']
            ],
            new Result(),
            1
        );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'TranslationKey' => 'TEST_STR_ID'
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

        $entity = m::mock(TranslationKeyEntity::class);

        $command = UpdateCmd::create($cmdData);

        $this->repoMap['TranslationKey']
            ->shouldReceive('fetchUsingId')
            ->with($command)
            ->once()
            ->andReturn($entity);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Error processing translation key text. Invalid or unsupported language code');

        $this->sut->handleCommand($command);
    }
}
