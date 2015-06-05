<?php

/**
 * Update Irfo Gv Permit
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Irfo;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermitType;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;

/**
 * Update Irfo Gv Permit
 */
final class UpdateIrfoGvPermit extends AbstractCommandHandler
{
    protected $repoServiceName = 'IrfoGvPermit';

    public function handleCommand(CommandInterface $command)
    {
        $type = $this->getRepo()->getReference(IrfoGvPermitType::class, $command->getIrfoGvPermitType());
        $status = $this->getRepo()->getRefdataReference($command->getIrfoPermitStatus());

        $irfoGvPermit = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $irfoGvPermit->setIrfoGvPermitType($type);
        $irfoGvPermit->setIrfoPermitStatus($status);
        $irfoGvPermit->setYearRequired($command->getYearRequired());
        $irfoGvPermit->setIsFeeExempt($command->getIsFeeExempt());
        $irfoGvPermit->setExemptionDetails($command->getExemptionDetails());
        $irfoGvPermit->setNoOfCopies($command->getNoOfCopies());

        if ($command->getInForceDate() !== null) {
            $irfoGvPermit->setInForceDate(new \DateTime($command->getInForceDate()));
        }

        $this->getRepo()->save($irfoGvPermit);

        $result = new Result();
        $result->addId('irfoGvPermit', $irfoGvPermit->getId());
        $result->addMessage('IRFO GV Permit updated successfully');

        return $result;
    }
}
