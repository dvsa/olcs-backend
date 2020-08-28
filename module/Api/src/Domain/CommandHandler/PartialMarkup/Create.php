<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\PartialMarkup;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Repository\Partial;
use Dvsa\Olcs\Api\Entity\System\PartialMarkup;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\PartialMarkup\Create as CreateCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Repository\PartialMarkup as PartialMarkupRepo;
use Dvsa\Olcs\Api\Domain\Repository\Partial as PartialRepo;
use Dvsa\Olcs\Api\Domain\Repository\Language as LanguageRepo;

/**
 * Create partial markup entry
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
final class Create extends AbstractCommandHandler
{
    protected $repoServiceName = 'PartialMarkup';
    protected $extraRepos = ['Language', 'Partial'];

    /**
     * Create partial markup entry for a partial
     *
     * @param CommandInterface $command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var LanguageRepo        $languageRepo
         * @var PartialRepo         $partialRepo
         * @var PartialMarkupRepo   $partialMarkupRepo
         * @var Partial             $partial
         * @var CreateCmd           $command
         */

        $partialMarkupRepo = $this->getRepo('PartialMarkup');
        $partialRepo = $this->getRepo('Partial');
        $languageRepo = $this->getRepo('Language');

        $partial = $partialRepo->fetchById($command->getPartial());
        $language = $languageRepo->fetchById($command->getLanguage());

        $newPartialMarkup = PartialMarkup::create($language, $partial, $command->getMarkup());

        $partialMarkupRepo->save($newPartialMarkup);
        $newId = $newPartialMarkup->getId();

        $this->result->addId('PartialMarkup', $newId);
        $this->result->addMessage('Partial Markup Created: ' . $newId);

        return $this->result;
    }
}
