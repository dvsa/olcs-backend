<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Partial;

use Dvsa\Olcs\Api\Domain\Command\PartialMarkup\Create as CreatePartialMarkupCmd;
use Dvsa\Olcs\Api\Domain\Command\PartialMarkup\Update as UpdatePartialMarkupCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TranslationKey\HandleTranslationTrait;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\System\Partial as PartialEntity;
use Dvsa\Olcs\Transfer\Command\PartialMarkup\Update as UpdatePartialCmd;
use Dvsa\Olcs\Api\Domain\Repository\Partial as PartialRepo;

/**
 * Update a Partial and child markup translations
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
final class Update extends AbstractCommandHandler
{
    use HandleTranslationTrait;

    protected $repoServiceName = 'Partial';
    protected $extraRepos = ['PartialMarkup'];

    protected $createCmdClass = CreatePartialMarkupCmd::class;
    protected $updateCmdClass = UpdatePartialMarkupCmd::class;
    protected $parentName = 'partial';
    protected $textVar = 'markup';
    protected $childRepo = null;

    public function handleCommand(CommandInterface $command): Result
    {
        /**
         * @var UpdatePartialCmd $command
         * @var PartialEntity $partial
         * @var PartialRepo $repo
         */
        $repo = $this->getRepo();
        $this->childRepo = $this->getRepo('PartialMarkup');
        $partial = $repo->fetchUsingId($command);

        // Handled in included trait. Shared with TranslationKeyText
        $this->processTranslations($command->getTranslationsArray(), $partial);

        $this->result->addId('Partial', $partial->getId());
        $this->result->addMessage('Translations Updated');

        return $this->result;
    }
}
