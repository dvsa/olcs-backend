<?php

/**
 * DeleteSi
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Si;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractDeleteCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Delete Si
 */
final class DeleteSi extends AbstractDeleteCommandHandler
{
    protected $repoServiceName = 'SeriousInfringement';

    /**
     * Delete
     *
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $si = $this->getRepo()->fetchUsingId($command);

        if ($si->getCase()->isErru()) {
            throw new Exception\ValidationException(['This is an ERRU case']);
        }

        $this->getRepo()->delete($si);

        $result = new Result();
        $result->addId('id', $si->getId());
        $result->addMessage('Serious Infringement deleted');

        return $result;
    }
}
