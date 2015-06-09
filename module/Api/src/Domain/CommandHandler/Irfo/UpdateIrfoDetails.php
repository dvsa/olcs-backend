<?php

/**
 * Update IrfoDetails
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Irfo;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Organisation\UpdateTradingNames;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPartner;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;

/**
 * Update IrfoDetails
 */
final class UpdateIrfoDetails extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Organisation';

    protected $extraRepos = ['IrfoPartner'];

    public function handleCommand(CommandInterface $command)
    {
        $org = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        if ($command->getIrfoNationality() !== null) {
            $org->setIrfoNationality(
                $this->getRepo()->getReference(Country::class, $command->getIrfoNationality())
            );
        }

        $this->getRepo()->save($org);

        if ($command->getTradingNames() !== null) {
            // deal with TradingNames
            $this->processTradingNames($org, $command->getTradingNames());
        }

        if ($command->getIrfoPartners() !== null) {
            // deal with IrfoPartners
            $this->processIrfoPartners($org, $command->getIrfoPartners());
        }

        $result = new Result();
        $result->addId('organisation', $org->getId());
        $result->addMessage('IRFO Details updated successfully');

        return $result;
    }

    /**
     * @param Organisation $org
     * @param array $tradingNames
     * @return array
     */
    private function processTradingNames(Organisation $org, array $tradingNames)
    {
        return $this->getCommandHandler()->handleCommand(
            UpdateTradingNames::create(
                [
                    'organisation' => $org->getId(),
                    'tradingNames' => array_column($tradingNames, 'name')
                ]
            )
        );
    }

    /**
     * @param Organisation $org
     * @param array $irfoPartners
     * @return array
     */
    private function processIrfoPartners(Organisation $org, array $irfoPartners)
    {
        $reduced = [];

        foreach ($irfoPartners as $irfoPartner) {
            if (empty($irfoPartner['name'])) {
                // filter out empty values
                continue;
            }

            if (!empty($irfoPartner['id'])) {
                // update
                $irfoPartnerEntity = $this->getRepo('IrfoPartner')->fetchById(
                    $irfoPartner['id'],
                    Query::HYDRATE_OBJECT,
                    $irfoPartner['version']
                );
                $irfoPartnerEntity->setName($irfoPartner['name']);
            } else {
                // create
                $irfoPartnerEntity = new IrfoPartner(
                    $org,
                    $irfoPartner['name']
                );
            }

            $this->getRepo('IrfoPartner')->save($irfoPartnerEntity);
            $reduced[] = $irfoPartnerEntity->getId();
        }

        // remove the rest
        foreach ($org->getIrfoPartners() as $irfoPartnerEntity) {
            if (!in_array($irfoPartnerEntity->getId(), $reduced)) {
                $this->getRepo('IrfoPartner')->delete($irfoPartnerEntity);
            }
        }

        return $reduced;
    }
}
