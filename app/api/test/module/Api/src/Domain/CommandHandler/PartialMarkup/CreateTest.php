<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\PartialMarkup;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\PartialMarkup\Create as CreateHandler;
use Dvsa\Olcs\Api\Domain\Repository\Partial as PartialRepo;
use Dvsa\Olcs\Api\Domain\Repository\PartialMarkup as PartialMarkupRepo;
use Dvsa\Olcs\Api\Domain\Repository\Language as LanguageRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\PartialMarkup\Create as CreateCmd;
use Dvsa\Olcs\Api\Entity\System\Partial as PartialEntity;
use Dvsa\Olcs\Api\Entity\System\PartialMarkup as PartialMarkupEntity;
use Dvsa\Olcs\Api\Entity\System\Language as LanguageEntity;

/**
 * Create PartialMarkup Test
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
class CreateTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new CreateHandler();
        $this->mockRepo('Partial', PartialRepo::class);
        $this->mockRepo('PartialMarkup', PartialMarkupRepo::class);
        $this->mockRepo('Language', LanguageRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $partial = 'TEST_STR_ID';
        $language = 1;
        $markup = 'This is some text';

        $cmdData = [
            'partial' => $partial,
            'language' => $language,
            'markup' => $markup,
        ];

        $command = CreateCmd::create($cmdData);

        $entity = m::mock(PartialMarkupEntity::class)->makePartial();
        $entity->setId(555);

        $partialEntity = m::mock(PartialEntity::class);
        $languageEntity = m::mock(LanguageEntity::class);

        $this->repoMap['Partial']
            ->shouldReceive('fetchById')
            ->with($partial)
            ->once()
            ->andReturn($partialEntity);

        $this->repoMap['Language']
            ->shouldReceive('fetchById')
            ->with($language)
            ->once()
            ->andReturn($languageEntity);

        $entity->shouldReceive('getId')
            ->andReturn(55);

        $this->repoMap['PartialMarkup']
            ->shouldReceive('save')
            ->with(m::type(PartialMarkupEntity::class))
            ->once()
            ->andReturnUsing(
                function (PartialMarkupEntity $partialMarkup) use ($languageEntity, $partialEntity, $markup) {
                    $partialMarkup->setId(555);
                    $this->assertEquals($markup, $partialMarkup->getMarkup());
                    $this->assertEquals($languageEntity, $partialMarkup->getLanguage());
                    $this->assertEquals($partialEntity, $partialMarkup->getPartial());
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'PartialMarkup' => 555
            ],
            'messages' => [
                'Partial Markup Created: 555'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
