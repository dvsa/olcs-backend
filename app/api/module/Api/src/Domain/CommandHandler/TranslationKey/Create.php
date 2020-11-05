<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\TranslationKey;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\System\TranslationKey;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\TranslationKey\Create as CreateTranslationKeyCmd;
use Dvsa\Olcs\Transfer\Command\TranslationKey\Update as UpdateTranslationKeyCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Create Translation key
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
final class Create extends AbstractCommandHandler
{
    protected $repoServiceName = 'TranslationKey';

    /**
     * @param CommandInterface $command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var CreateTranslationKeyCmd $command
         */
        $repo = $this->getRepo('TranslationKey');

        try {
            $repo->save(TranslationKey::create($command->getId(), $command->getDescription()));
        } catch (\Exception $e) {
            throw new NotFoundException('editable-translations-cant-save');
        }

        $this->result->addId('TranslationKey', $command->getId());
        $this->result->addMessage('TranslationKey created');

        // Add the translation Texts to the newly created key, this also regenerates the redis cache.
        $this->handleSideEffect(UpdateTranslationKeyCmd::create($command->getArrayCopy()));

        return $this->result;
    }
}
