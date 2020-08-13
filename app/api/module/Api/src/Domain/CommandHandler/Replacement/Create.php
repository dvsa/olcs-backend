<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Replacement;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\System\Replacement as ReplacementEntity;

/**
 * Create a Replacement
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
final class Create extends AbstractCommandHandler
{
    protected $repoServiceName = 'Replacement';

    /**
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command): Result
    {
        $replacement = ReplacementEntity::create(
            $command->getPlaceholder(),
            $command->getReplacementText()
        );

        $this->getRepo()->save($replacement);

        $this->result->addId('Replacement', $replacement->getId());
        $this->result->addMessage("Replacement '{$replacement->getId()}' created");

        return $this->result;
    }
}
