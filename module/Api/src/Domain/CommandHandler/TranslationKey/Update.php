<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\TranslationKey;

use Dvsa\Olcs\Api\Domain\Command\TranslationKeyText\Create as CreateTranslationKeyTextCmd;
use Dvsa\Olcs\Api\Domain\Command\TranslationKeyText\Update as UpdateTranslationKeyTextCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\System\Language;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\System\TranslationKey as TranslationKeyEntity;
use Dvsa\Olcs\Api\Entity\System\TranslationKeyText as TranslationKeyTextEntity;
use Dvsa\Olcs\Transfer\Command\TranslationKey\Update as UpdateTranslationKeyCmd;
use Dvsa\Olcs\Api\Domain\Repository\TranslationKey as TranslationKeyRepo;
use Dvsa\Olcs\Api\Domain\Repository\TranslationKeyText as TranslationKeyTextRepo;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;

/**
 * Update a Translation Key and child translations
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
final class Update extends AbstractCommandHandler
{
    protected $repoServiceName = 'TranslationKey';
    protected $extraRepos = ['TranslationKeyText', 'Language'];

    public function handleCommand(CommandInterface $command): Result
    {
        /**
         * @var UpdateTranslationKeyCmd $command
         * @var TranslationKeyEntity $translationKey
         * @var TranslationKeyRepo $repo
         */

        $repo = $this->getRepo();
        $translationKey = $repo->fetchUsingId($command);

        $this->processTranslations($command, $translationKey);

        $this->result->addId('TranslationKey', $translationKey->getId());
        $this->result->addMessage('Translations Updated');

        return $this->result;
    }

    /**
     * @param CommandInterface $command
     * @param TranslationKeyEntity $translationKey
     */
    protected function processTranslations(CommandInterface $command, TranslationKeyEntity $translationKey)
    {
        /**
         * @var UpdateTranslationKeyCmd $command
         * @var TranslationKeyEntity $translationKey
         */

        foreach ($command->getTranslationsArray() as $isoCode => $translatedText) {
            if (array_key_exists($isoCode, Language::SUPPORTED_LANGUAGES)) {
                $this->updateOrCreate($translationKey->getId(), Language::SUPPORTED_LANGUAGES[$isoCode]['id'], $translatedText);
            } else {
                throw new RuntimeException('Error processing translation key text. Invalid or unsupported language code');
            }
        }
    }

    /**
     * @param string $translationKey
     * @param int $languageId
     * @param string $translatedText
     */
    protected function updateOrCreate(string $translationKeyId, int $languageId, string $translatedText)
    {
        /**
         * @var TranslationKeyEntity $translationKeycoo
         * @var TranslationKeyTextEntity $translationKeyTextRecord
         * @var TranslationKeyTextRepo $transKeyTextRepo
         */

        $transKeyTextRepo = $this->getRepo('TranslationKeyText');

        $translationKeyTextRecord = $transKeyTextRepo->fetchByTranslationKeyLanguage($translationKeyId, $languageId);
        if (empty($translationKeyTextRecord)) {
            $this->result->merge($this->handleSideEffect(
                CreateTranslationKeyTextCmd::create(
                    [
                        'translationKey' => $translationKeyId,
                        'language' => $languageId,
                        'translatedText' => $translatedText
                    ]
                )
            ));
        } else {
            $this->result->merge($this->handleSideEffect(
                UpdateTranslationKeyTextCmd::create(
                    [
                        'id' => $translationKeyTextRecord->getId(),
                        'translatedText' => $translatedText
                    ]
                )
            ));
        }
    }
}
