<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\LicenceStatusRule;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Class DeleteLicenceStatusRule
 *
 * Create a licence status rule.
 *
 * @package Dvsa\Olcs\Api\Domain\CommandHandler\LicenceStatusRule
 */
final class DeleteLicenceStatusRule extends AbstractCommandHandler
{
    protected $repoServiceName = 'LicenceStatusRule';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $this->getRepo()->delete(
            $this->getRepo()->fetchById($command->getId())
        );

        $result->addId('licence-status-rule', $command->getId());
        $result->addMessage('Licence status rule deleted.');

        return $result;
    }
}
