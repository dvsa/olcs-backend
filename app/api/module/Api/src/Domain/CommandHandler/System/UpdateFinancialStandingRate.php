<?php

/**
 * Update Financial Standing Rate
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\System;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Entity\System\FinancialStandingRate as Entity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\System\UpdateFinancialStandingRate as Cmd;

/**
 * Update Financial Standing Rate
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class UpdateFinancialStandingRate extends AbstractCommandHandler
{
    protected $repoServiceName = 'FinancialStandingRate';

    public function handleCommand(CommandInterface $command)
    {
        $rate = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $rate
            ->setGoodsOrPsv($this->getRepo()->getRefdataReference($command->getGoodsOrPsv()))
            ->setLicenceType($this->getRepo()->getRefdataReference($command->getLicenceType()))
            ->setFirstVehicleRate($command->getFirstVehicleRate())
            ->setAdditionalVehicleRate($command->getAdditionalVehicleRate())
            ->setEffectiveFrom(new \DateTime($command->getEffectiveFrom()));

        $this->getRepo()->save($rate);

        $result = new Result();
        $result->addId('financialStandingRate', $rate->getId());
        $result->addMessage('Financial Standing Rate updated');

        return $result;
    }
}
