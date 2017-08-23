<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\DataRetention;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Repository\DataRetentionRule;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Class Populate
 */
final class Populate extends AbstractCommandHandler implements TransactionedInterface, AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'DataRetentionRule';

    /**
     * Handle command
     *
     * @param CommandInterface $command DTO
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var DataRetentionRule $repo */
        $repo = $this->getRepo();

        /** @var \Dvsa\Olcs\Api\Entity\DataRetentionRule $dataRetentionRule */
        foreach ($repo->fetchEnabledRules()['results'] as $dataRetentionRule) {
            $this->result->addMessage(
                sprintf(
                    'Running rule id %s, %s',
                    $dataRetentionRule->getId(),
                    $dataRetentionRule->getPopulateProcedure()
                )
            );
            $repo->runProc(
                $dataRetentionRule->getPopulateProcedure(),
                $this->getCurrentUser()->getId()
            );
        }

        return $this->result;
    }
}
