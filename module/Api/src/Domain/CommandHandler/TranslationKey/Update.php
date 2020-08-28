<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\TranslationKey;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Command\TranslationKeyText\Create as CreateTranslationKeyTextCmd;
use Dvsa\Olcs\Api\Domain\Command\TranslationKeyText\Update as UpdateTranslationKeyTextCmd;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\System\TranslationKey as TranslationKeyEntity;
use Dvsa\Olcs\Transfer\Command\TranslationKey\Update as UpdateTranslationKeyCmd;
use Dvsa\Olcs\Api\Domain\Repository\TranslationKey as TranslationKeyRepo;

/**
 * Update a Translation Key and child translations
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
final class Update extends AbstractCommandHandler
{
    use HandleTranslationTrait;

    protected $repoServiceName = 'TranslationKey';
    protected $extraRepos = ['TranslationKeyText'];

    protected $createCmdClass = CreateTranslationKeyTextCmd::class;
    protected $updateCmdClass = UpdateTranslationKeyTextCmd::class;
    protected $parentName = 'translationKey';
    protected $textVar = 'translatedText';
    protected $childRepo = null;

    public function handleCommand(CommandInterface $command): Result
    {
        /**
         * @var UpdateTranslationKeyCmd $command
         * @var TranslationKeyEntity $translationKey
         * @var TranslationKeyRepo $repo
         */

        $repo = $this->getRepo();
        $this->childRepo = $this->getRepo('TranslationKeyText');
        $translationKey = $repo->fetchUsingId($command);

        // Handled in included trait. Shared with PartialMarkup
        $this->processTranslations($command->getTranslationsArray(), $translationKey);

        $this->result->addId('TranslationKey', $translationKey->getId());
        $this->result->addMessage('Translations Updated');

        return $this->result;
    }
}
