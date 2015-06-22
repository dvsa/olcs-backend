<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\LicenceStatusRule;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Licence\LicenceStatusRule;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Class CreateLicenceStatusRule
 *
 * Create a licence status rule.
 *
 * @package Dvsa\Olcs\Api\Domain\CommandHandler\LicenceStatusRule
 */
final class CreateLicenceStatusRule extends AbstractCommandHandler
{
    protected $repoServiceName = 'LicenceStatusRule';

    public function handleCommand(CommandInterface $command)
    {
        $licence = $this->getRepo()
            ->getReference(Licence::class, $command->getLicence());

        $status = $this->getRepo()->getRefdataReference($command->getStatus());

        $statusRule = new LicenceStatusRule();
        $statusRule->setLicenceStatus($status);
        $statusRule->setStartDate(new \DateTime($command->getStartDate()));
        $statusRule->setEndDate(new \DateTime($command->getEndDate()));
        $statusRule->setLicence($licence);

        $this->getRepo()->save($statusRule);

        $result = new Result();
        $result->addId('licence-status-rule', $statusRule->getId());
        $result->addMessage('Licence status rule created successfully');

        return $result;
    }
}