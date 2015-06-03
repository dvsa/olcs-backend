<?php

/**
 * Create Irfo Gv Permit
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Irfo;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermit;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermitType;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Transfer\Command\Irfo\CreateIrfoGvPermit as Cmd;

/**
 * Create Irfo Gv Permit
 */
final class CreateIrfoGvPermit extends AbstractCommandHandler
{
    protected $repoServiceName = 'IrfoGvPermit';

    public function handleCommand(CommandInterface $command)
    {
        $irfoGvPermit = $this->createIrfoGvPermitObject($command);

        $this->getRepo()->save($irfoGvPermit);

        $result = new Result();
        $result->addId('irfoGvPermit', $irfoGvPermit->getId());
        $result->addMessage('IRFO GV Permit created successfully');

        return $result;
    }

    /**
     * @param Cmd $command
     * @return IrfoGvPermit
     */
    private function createIrfoGvPermitObject(Cmd $command)
    {
        $organisation = $this->getRepo()->getReference(Organisation::class, $command->getOrganisation());
        $type = $this->getRepo()->getReference(IrfoGvPermitType::class, $command->getIrfoGvPermitType());
        $status = $this->getRepo()->getRefdataReference($command->getIrfoPermitStatus());

        $irfoGvPermit = new IrfoGvPermit($organisation, $type, $status);

        $irfoGvPermit->setYearRequired($command->getYearRequired());
        $irfoGvPermit->setIsFeeExempt($command->getIsFeeExempt());
        $irfoGvPermit->setExemptionDetails($command->getExemptionDetails());
        $irfoGvPermit->setNoOfCopies($command->getNoOfCopies());

        if ($command->getInForceDate() !== null) {
            $irfoGvPermit->setInForceDate(new \DateTime($command->getInForceDate()));
        }

        return $irfoGvPermit;
    }
}
