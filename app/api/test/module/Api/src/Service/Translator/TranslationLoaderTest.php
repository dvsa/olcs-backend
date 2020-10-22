<?php

namespace Dvsa\OlcsTest\Api\Service\Translator;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Repository\TranslationKeyText;
use Dvsa\Olcs\Api\Service\Translator\TranslationLoader;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\I18n\Translator\TextDomain;

/**
 * TranslationLoaderTest
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class TranslationLoaderTest extends MockeryTestCase
{
    /**
     * test loading translations from the cache
     */
    public function testLoadFromCache()
    {
        $locale = 'en_GB';
        $textDomain = 'default';
        $actualMessages = ['some_key' => 'some_text'];
        $cacheIdentifier = CacheEncryption::TRANSLATION_KEY_IDENTIFIER;

        $messages = [
            $textDomain => [
                $locale => $actualMessages,
            ],
        ];

        $mockCache = m::mock(CacheEncryption::class);
        $mockCache->expects('getCustomItem')
            ->with($cacheIdentifier, $locale)
            ->andReturn($messages);

        $mockRepo = m::mock(TranslationKeyText::class);

        $loader = new TranslationLoader($mockCache, $mockRepo);
        $textDomain = $loader->load($locale, $textDomain);

        self::assertInstanceOf(TextDomain::class, $textDomain);
        self::assertSame($actualMessages, $textDomain->getArrayCopy());
    }

    /**
     * test loading translations from the database it the cache is empty
     */
    public function testLoadFromDatabase()
    {
        $locale = 'en_GB';
        $textDomain = 'default';
        $actualMessages = $this->actualMessages();
        $cacheIdentifier = CacheEncryption::TRANSLATION_KEY_IDENTIFIER;

        $messages = [
            $textDomain => [
                $locale => $actualMessages,
            ],
        ];

        $dbTranslations = $this->dbTranslations($locale);

        $mockCache = m::mock(CacheEncryption::class);
        $mockCache->expects('getCustomItem')
            ->with($cacheIdentifier, $locale)
            ->andReturnNull();
        $mockCache->expects('setCustomItem')->with($cacheIdentifier, $messages, $locale);

        $mockRepo = m::mock(TranslationKeyText::class);
        $mockRepo->expects('fetchAll')->with($locale, Query::HYDRATE_ARRAY)->andReturn($dbTranslations);

        $loader = new TranslationLoader($mockCache, $mockRepo);
        $textDomain = $loader->load($locale, $textDomain);

        self::assertInstanceOf(TextDomain::class, $textDomain);
        self::assertSame($actualMessages, $textDomain->getArrayCopy());
    }

    /**
     * test loading translations from the database when cache is down (exceptions on load/save)
     */
    public function testCacheExceptionsStillLoadFromDatabase()
    {
        $locale = 'en_GB';
        $textDomain = 'default';
        $actualMessages = $this->actualMessages();
        $cacheIdentifier = CacheEncryption::TRANSLATION_KEY_IDENTIFIER;

        $messages = [
            $textDomain => [
                $locale => $actualMessages,
            ],
        ];

        $dbTranslations = $this->dbTranslations($locale);

        $mockCache = m::mock(CacheEncryption::class);
        $mockCache->expects('getCustomItem')
            ->with($cacheIdentifier, $locale)
            ->andThrow(\Exception::class);
        $mockCache->expects('setCustomItem')
            ->with($cacheIdentifier, $messages, $locale)
            ->andThrow(\Exception::class);

        $mockRepo = m::mock(TranslationKeyText::class);
        $mockRepo->expects('fetchAll')->with($locale, Query::HYDRATE_ARRAY)->andReturn($dbTranslations);

        $loader = new TranslationLoader($mockCache, $mockRepo);
        $textDomain = $loader->load($locale, $textDomain);

        self::assertInstanceOf(TextDomain::class, $textDomain);
        self::assertSame($actualMessages, $textDomain->getArrayCopy());
    }

    private function actualMessages()
    {
        return [
            'translation_key1' => 'translated_text1',
            'translation_key2' => 'translated_text2'
        ];
    }

    private function dbTranslations($locale)
    {
        return [
            0 => [
                'language' => [
                    'isoCode' => $locale,
                ],
                'translationKey' => [
                    'id' => 'translation_key1',
                ],
                'translatedText' => 'translated_text1',
            ],
            1 => [
                'language' => [
                    'isoCode' => $locale,
                ],
                'translationKey' => [
                    'id' => 'translation_key2',
                ],
                'translatedText' => 'translated_text2',
            ],
        ];
    }
}
