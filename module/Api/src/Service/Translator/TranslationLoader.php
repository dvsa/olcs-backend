<?php

namespace Dvsa\Olcs\Api\Service\Translator;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Repository\TranslationKeyText as TranslationKeyTextRepo;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Olcs\Logging\Log\Logger;
use Zend\I18n\Translator\Loader\PhpMemoryArray;
use Zend\I18n\Translator\Loader\RemoteLoaderInterface;
use Zend\I18n\Translator\TextDomain;

/**
 * Translation loader service for API nodes
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class TranslationLoader implements RemoteLoaderInterface
{
    const ERR_CACHE_LOAD = 'Translation cache load failure: %s';
    const ERR_CACHE_SAVE = 'Translation cache save failure: %s';
    const DEFAULT_TEXT_DOMAIN = 'default';
    const SUPPORTED_LOCALES = [
        'en_GB',
        'cy_GB',
        'en_NI',
        'cy_NI',
    ];

    /** @var CacheEncryption $cache */
    private $cache;

    /** @var TranslationKeyTextRepo $translationKeyRepo */
    private $translationKeyRepo;

    /**
     * TranslationLoader constructor.
     *
     * @param CacheEncryption $cache
     * @param TranslationKeyTextRepo $translationKeyRepo
     */
    public function __construct(CacheEncryption $cache, TranslationKeyTextRepo $translationKeyRepo)
    {
        $this->cache = $cache;
        $this->translationKeyRepo = $translationKeyRepo;
    }

    /**
     * Load translation information based on the locale
     *
     * @param string $locale
     * @param string $textDomain needed to comply with interface but not needed by us
     *
     * @return TextDomain
     * @throws \Exception
     */
    public function load($locale, $textDomain)
    {
        $messages = $this->getMessages($locale, $textDomain);
        $zendMemoryArray = new PhpMemoryArray($messages);

        return $zendMemoryArray->load($locale, $textDomain);
    }

    /**
     * Get translation messages, try the cache first, fall back to the DB
     *
     * @param string $locale
     * @param string $textDomain
     *
     * @return array
     */
    public function getMessages(string $locale, string $textDomain): array
    {
        try {
            $messages = $this->cache->getCustomItem(CacheEncryption::TRANSLATION_KEY_IDENTIFIER, $locale);
        } catch (\Exception $e) {
            $messages = null;
            $errorMessage = sprintf(self::ERR_CACHE_LOAD, $e->getMessage());
            Logger::err($errorMessage);
        }

        //if there has been a problem with the Redis cache, fall back to the database, repopulate cache
        if (!$messages) {
            $messages = $this->getMessagesFromDb($locale, $textDomain);

            try {
                $this->cache->setCustomItem(CacheEncryption::TRANSLATION_KEY_IDENTIFIER, $messages, $locale);
            } catch (\Exception $e) {
                $errorMessage = sprintf(self::ERR_CACHE_SAVE, $e->getMessage());
                Logger::err($errorMessage);
            }
        }

        return $messages;
    }

    /**
     * Get translation messages from the database
     *
     * @param string $locale
     * @param string $textDomain
     *
     * @return array
     */
    public function getMessagesFromDb(string $locale, string $textDomain): array
    {
        $translationKeys = $this->translationKeyRepo->fetchAll($locale, Query::HYDRATE_ARRAY);

        $messages = [];

        foreach ($translationKeys as $key) {
            $messages[$textDomain][$key['language']['isoCode']][$key['translationKey']['id']] = $key['translatedText'];
        }

        return $messages;
    }
}
