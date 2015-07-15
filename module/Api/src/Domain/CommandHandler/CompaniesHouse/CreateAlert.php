<?php

/**
 * Companies House CreateAlert
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\CompaniesHouse;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\CompaniesHouse\CompaniesHouseAlert as AlertEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class CreateAlert extends AbstractCommandHandler
{
    protected $repoServiceName = 'CompaniesHouseAlert';

    /**
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $alert = new AlertEntity();
        $alert
            ->setCompanyOrLlpNo($command->getCompanyNumber())
            ->setOrganisation($command->getOrganisation());

        foreach ($command->getReasons() as $reason) {
            $reasonRefdata = $this->getRepo()->getRefdataReference($reason);
            $alert->addReason($reasonRefdata);
        }

        $this->getRepo('CompaniesHouseAlert')->save($alert);

        $result = new Result();
        $result
            ->addId('companiesHouseAlert', $alert->getId())
            ->addMessage('Alert created: ' . json_encode($command->getReasons()));

        return $result;
    }
}
