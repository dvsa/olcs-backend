<?php

/**
 * Create Financial Standing Rate
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\System;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
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
        $this->checkForDuplicate($command);

        $rate = $this->createObject($command);

        $this->getRepo()->save($rate);

        $result = new Result();
        $result->addId('financialStandingRate', $rate->getId());
        $result->addMessage('Financial Standing Rate created');

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
            ->setAdditionalVehicleRate($command->getAdditionalVehicleRate())
            ->setEffectiveFrom(new \DateTime($command->getEffectiveFrom()));

        return $rate;
    }

    private function checkForDuplicate(CommandInterface $command)
    {
        $existing = $this->getRepo()->fetchByCategoryTypeAndDate(
            $command->getGoodsOrPsv(),
            $command->getLicenceType(),
            $command->getEffectiveFrom()
        );

        if ($existing) {
            // duplicate detected
            $msg = 'A rate for this operator type, licence type and effective date already exists';
            throw new ValidationException([$msg]);
        }
    }
}
