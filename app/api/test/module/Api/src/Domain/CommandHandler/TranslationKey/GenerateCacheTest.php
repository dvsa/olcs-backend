<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\TranslationKey;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Dvsa\Olcs\Transfer\Command\TranslationKey\GenerateCache as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\TranslationKey\GenerateCache as Handler;
use Dvsa\Olcs\Api\Service\Translator\TranslationLoader;
use Mockery as m;
use Zend\I18n\Translator\LoaderPluginManager;
use Zend\Mvc\I18n\Translator;

/**
 * Test cache generation for each locale
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class GenerateCacheTest extends CommandHandlerTestCase
{
    private $translationLoader;
    private $cacheService;
    private $translator;

    public function setUp(): void
    {
        $this->sut = new Handler();
        $this->translationLoader = m::mock(TranslationLoader::class);
        $this->cacheService = m::mock(CacheEncryption::class);
        $this->translator = m::mock(Translator::class);

        $pluginManager = m::mock(LoaderPluginManager::class);
        $pluginManager->expects('get')->with(TranslationLoader::class)->andReturn($this->translationLoader);

        $this->mockedSmServices = [
            'TranslatorPluginManager' => $pluginManager,
            CacheEncryption::class => $this->cacheService,
            'translator' => $this->translator,
        ];

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $expectedMessages = [];

        /**
         * For each locale that we support, create an assertion for the load, the save, and the zend cache delete
         */
        foreach (TranslationLoader::SUPPORTED_LOCALES as $locale) {
            $this->translationLoader->expects('getMessagesFromDb')
                ->with($locale, TranslationLoader::DEFAULT_TEXT_DOMAIN)
                ->andReturn(['messages' . $locale]);

            $this->cacheService->expects('setCustomItem')
                ->with(
                    CacheEncryption::TRANSLATION_KEY_IDENTIFIER,
                    ['messages' . $locale],
                    $locale
                );

            $this->translator->expects('clearCache')->with(TranslationLoader::DEFAULT_TEXT_DOMAIN, $locale);

            $expectedMessages[] = sprintf(Handler::UPDATE_MSG, $locale);
        }

        $command = Cmd::create([]);
        $result = $this->sut->handleCommand($command);

        $this->assertEquals($expectedMessages, $result->getMessages());
    }
}
