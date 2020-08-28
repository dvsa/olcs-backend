<?php
/**
 * Shared Logic for TranslationKey and Partial language updates
 */

namespace Dvsa\Olcs\Api\Domain\CommandHandler\TranslationKey;

use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Entity\System\Language;

trait HandleTranslationTrait
{
    /**
     * @param array TranslationsArray
     * @param $parentEntity
     */
    protected function processTranslations(array $translationsArray, $parentEntity)
    {
        foreach ($translationsArray as $isoCode => $translatedText) {
            if (array_key_exists($isoCode, Language::SUPPORTED_LANGUAGES)) {
                $this->updateOrCreate($parentEntity->getId(), Language::SUPPORTED_LANGUAGES[$isoCode]['id'], $translatedText);
            } else {
                throw new RuntimeException('Error processing translation. Invalid or unsupported language code');
            }
        }
    }

    /**
     * @param mixed $parentEntityId
     * @param int $languageId
     * @param string $translatedText
     */
    protected function updateOrCreate($parentEntityId, int $languageId, string $translatedText)
    {
        $transRecord = $this->childRepo->fetchByParentLanguage($parentEntityId, $languageId);
        if (empty($transRecord)) {
            $this->result->merge($this->handleSideEffect(
                $this->createCmdClass::create(
                    [
                        "{$this->parentName}" => $parentEntityId,
                        'language' => $languageId,
                        "{$this->textVar}" => $translatedText
                    ]
                )
            ));
        } else {
            $this->result->merge($this->handleSideEffect(
                $this->updateCmdClass::create(
                    [
                        'id' => $transRecord->getId(),
                        "{$this->textVar}" => $translatedText
                    ]
                )
            ));
        }
    }
}
