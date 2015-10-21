<?php

/**
 * Create Fee
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Fee;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Bus\BusReg;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermit;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuth;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Task\Task;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Create Fee
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateFee extends AbstractCommandHandler
{
    protected $repoServiceName = 'Fee';

    public function handleCommand(CommandInterface $command)
    {
        $fee = $this->createFeeObject($command);

        $this->getRepo()->save($fee);

        $result = new Result();
        $result->addId('fee', $fee->getId());
        $result->addMessage('Fee created successfully');

        return $result;
    }

    /**
     * @param Cmd $command
     * @return Fee
     */
    private function createFeeObject(Cmd $command)
    {
        $feeType = $this->getRepo()->getReference(FeeType::class, $command->getFeeType());
        $feeStatus = $this->getRepo()->getRefdataReference($command->getFeeStatus());

        $fee = new Fee($feeType, $command->getAmount(), $feeStatus);

        if ($command->getTask() !== null) {
            $fee->setTask($this->getRepo()->getReference(Task::class, $command->getTask()));
        }

        if ($command->getLicence() !== null) {
            $fee->setLicence($this->getRepo()->getReference(Licence::class, $command->getLicence()));
        }

        if ($command->getInvoicedDate() !== null) {
            $fee->setInvoicedDate(new \DateTime($command->getInvoicedDate()));
        }

        if ($command->getIrfoGvPermit() !== null) {
            $fee->setIrfoGvPermit($this->getRepo()->getReference(IrfoGvPermit::class, $command->getIrfoGvPermit()));
        }

        if ($command->getIrfoPsvAuth() !== null) {
            $fee->setIrfoPsvAuth($this->getRepo()->getReference(IrfoPsvAuth::class, $command->getIrfoPsvAuth()));
        }

        if ($command->getApplication() !== null) {
            $application = $this->getRepo()->getReference(Application::class, $command->getApplication());
            $fee->setApplication($application);
            if (!$fee->getLicence()) {
                // if licence id wasn't specified, link the application's licence
                $fee->setLicence($application->getLicence());
            }
        }

        if ($command->getBusReg() !== null) {
            $busReg = $this->getRepo()->getReference(BusReg::class, $command->getBusReg());
            $fee->setBusReg($busReg);
            if (!$fee->getLicence()) {
                // if licence id wasn't specified, link the bus reg's licence
                $fee->setLicence($busReg->getLicence());
            }
        }

        if ($command->getUser() !== null) {

            $fee->setCreatedBy($this->getRepo()->getReference(User::class, $command->getUser()));
        }

        if ($command->getDescription() !== null) {
            $fee->setDescription($command->getDescription());
        } else {
            $fee->setDescription($feeType->getDescription());
        }

        // if amount is null, we should use the amount from the feeType
        if (is_null($fee->getAmount())) {
            $fee->setAmount($feeType->getAmount());
        }

        return $fee;
    }
}
