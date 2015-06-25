<?php

/**
 * RemoveLicenceStatusRulesForLicence.php
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\LicenceStatusRule;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\LicenceStatusRule\DeleteLicenceStatusRule;

/**
 * Remove licence status rules for a licence.
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
final class RemoveLicenceStatusRulesForLicence extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'LicenceStatusRule';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $rules = $this->getRepo()->fetchForLicence($command->getLicence());

        foreach ($rules as $statusRule) {
            $result->merge(
                $this->handleSideEffect(
                    DeleteLicenceStatusRule::create(
                        [
                            'id' => $statusRule->getId()
                        ]
                    )
                )
            );
        }

        return $result;
    }
}
