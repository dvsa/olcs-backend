<?php

/**
 * Companies House CreateAlert
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\CompaniesHouse;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler as DomainAbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Entity\CompaniesHouse\CompaniesHouseAlert as AlertEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class CreateAlert extends DomainAbstractCommandHandler
{
    protected $repoServiceName = 'CompaniesHouseAlert';

    protected $extraRepos = ['Organisation'];

    /**
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();
        $companyNumber = $command->getCompanyNumber();

        try {
            $organisation = $this->getOrganisation($companyNumber);
        } catch (NotFoundException $e) {
            $result->addMessage("Organisation not found for company $companyNumber, no alert created");
            return $result;
        }

        $alert = new AlertEntity();
        $alert
            ->setCompanyOrLlpNo($companyNumber)
            ->setOrganisation($organisation);

        foreach ($command->getReasons() as $reason) {
            $reasonRefdata = $this->getRepo()->getRefdataReference($reason);
            $alert->addReason($reasonRefdata);
        }

        $this->getRepo('CompaniesHouseAlert')->save($alert);

        $result
            ->addId('companiesHouseAlert', $alert->getId())
            ->addMessage('Alert created: ' . json_encode($command->getReasons()));

        return $result;
    }

    /**
     * @param string $companyNumber
     * @return OrganisationEntity|false
     */
    protected function getOrganisation($companyNumber)
    {
        $results = $this->getRepo('Organisation')->getByCompanyOrLlpNo($companyNumber);

        // @note returns the first matching organisation only
        return !empty($results) ? $results[0] : false;
    }
}
