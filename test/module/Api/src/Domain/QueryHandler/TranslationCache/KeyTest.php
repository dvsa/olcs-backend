<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\TranslationCache;

use Dvsa\Olcs\Api\Service\Translator\TranslationLoader;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\QueryHandler\TranslationCache\Key as Handler;
use Dvsa\Olcs\Transfer\Query\TranslationCache\Key as Qry;
use Mockery as m;
use Zend\I18n\Translator\LoaderPluginManager;

/**
 * Tests the translation cache query handler calls the translation loader correctly
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class KeyTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new Handler();
        $translationLoader = m::mock(TranslationLoader::class);
        $translationLoader->expects('getMessages')
            ->with('en_GB', TranslationLoader::DEFAULT_TEXT_DOMAIN)
            ->andReturn(['messages']);

        $pluginManager = m::mock(LoaderPluginManager::class);
        $pluginManager->expects('get')->with(TranslationLoader::class)->andReturn($translationLoader);

        $this->mockedSmServices = [
            'TranslatorPluginManager' => $pluginManager,
        ];

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 'en_GB']);
        $this->assertEquals(['messages'], $this->sut->handleQuery($query));
    }
}
