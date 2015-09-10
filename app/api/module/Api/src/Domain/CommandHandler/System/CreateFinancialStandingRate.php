<?php

/**
 * Create Financial Standing Rate
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
use Dvsa\Olcs\Transfer\Command\System\CreateFinancialStandingRate as Cmd;

/**
 * Create Financial Standing Rate
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class CreateFinancialStandingRate extends AbstractCommandHandler
{
    protected $repoServiceName = 'FinancialStandingRate';

    public function handleCommand(CommandInterface $command)
    {
        $rate = $this->createObject($command);

        $this->getRepo()->save($rate);

        $result = new Result();
        $result->addId('FinancialStandingRate', $rate->getId());
        $result->addMessage('Financial Standing Rate created successfully');

        return $result;
    }

    /**
     * @param Cmd $command
     * @return FinancialStandingRate
     */
    private function createObject(Cmd $command)
    {
        $rate = new Entity();
        $rate
            ->setGoodsOrPsv($this->getRepo()->getRefdataReference($command->getGoodsOrPsv()))
            ->setLicenceType($this->getRepo()->getRefdataReference($command->getLicenceType()))
            ->setFirstVehicleRate($command->getFirstVehicleRate())
            ->setAdditionalVehicleRate($command->getAdditionalVehicleRate());

        if ($command->getEffectiveFrom() !== null) {
            $rate->setEffectiveFrom(new \DateTime($command->getEffectiveFrom()));
        }

        return $rate;
    }
}
