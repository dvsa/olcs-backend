<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Si\Applied;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Entity\Si\ErruRequest as ErruRequestEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Delete SiPenalty ("applied penalty" on the internal side)
 */
final class Delete extends AbstractCommandHandler
{
    protected $repoServiceName = 'SiPenalty';

    /**
     * Delete
     *
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $penalty = $this->getRepo()->fetchUsingId($command);

        $case = $penalty->getSeriousInfringement()->getCase();
        $erruRequest = $case->getErruRequest();

        if ($case->isClosed()
            || (($erruRequest instanceof ErruRequestEntity) && ($erruRequest->getResponseSent() === 'Y'))
        ) {
            throw new Exception\ValidationException(['Invalid action for the case']);
        }

        $this->getRepo()->delete($penalty);

        $result = new Result();
        $result->addId('id', $penalty->getId());
        $result->addMessage('Applied penalty deleted');

        return $result;
    }
}
