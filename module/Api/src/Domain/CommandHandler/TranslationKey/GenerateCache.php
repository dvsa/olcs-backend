<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\TranslationKey;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\CacheAwareInterface;
use Dvsa\Olcs\Api\Domain\CacheAwareTrait;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\TranslationLoaderAwareInterface;
use Dvsa\Olcs\Api\Domain\TranslationLoaderAwareTrait;
use Dvsa\Olcs\Api\Domain\TranslatorAwareInterface;
use Dvsa\Olcs\Api\Domain\TranslatorAwareTrait;
use Dvsa\Olcs\Api\Service\Translator\TranslationLoader;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;

/**
 * Create the translation key cache
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
final class GenerateCache extends AbstractCommandHandler implements CacheAwareInterface, TranslatorAwareInterface, TranslationLoaderAwareInterface
{
    use CacheAwareTrait;
    use TranslatorAwareTrait;
    use TranslationLoaderAwareTrait;

    const UPDATE_MSG = 'Translation key cache updated for %s';

    public function handleCommand(CommandInterface $command)
    {
        foreach (TranslationLoader::SUPPORTED_LOCALES as $locale) {
            //get latest messages from DB
            $messages = $this->translationLoader->getMessagesFromDb($locale, TranslationLoader::DEFAULT_TEXT_DOMAIN);

            //update translation loader cache
            $this->cacheService->setCustomItem(
                CacheEncryption::TRANSLATION_KEY_IDENTIFIER,
                $messages,
                $locale
            );

            //clear the zend translation cache - zend will recreate this based on the new translation loader contents
            $this->translator->clearCache(TranslationLoader::DEFAULT_TEXT_DOMAIN, $locale);

            $this->result->addMessage(sprintf(self::UPDATE_MSG, $locale));
        }

        return $this->result;
    }
}
