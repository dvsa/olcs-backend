<?php

/**
 * Create Fee
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Fee;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
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
use Dvsa\Olcs\Transfer\Command\Fee\CreateFee as Cmd;

/**
 * Create Fee
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateFee extends AbstractCommandHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'Fee';

    public function handleCommand(CommandInterface $command)
    {
        $feeType = $this->getRepo()->getReference(FeeType::class, $command->getFeeType());

        $this->validate($command, $feeType);

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

        $fee->setCreatedBy($this->getCurrentUser());

        if ($command->getInvoicedDate() !== null) {
            $fee->setInvoicedDate(new \DateTime($command->getInvoicedDate()));
        }

        if ($command->getDescription() !== null) {
            $fee->setDescription($command->getDescription());
        } else {
            $fee->setDescription($feeType->getDescription());
        }

        $this->handleTaskLink($command, $fee);
        $this->handleLicenceLink($command, $fee);
        $this->handleIrfoGvPermitLink($command, $fee);
        $this->handleIrfoPsvAuthLink($command, $fee);
        $this->handleApplicationLink($command, $fee);
        $this->handleBusRegLink($command, $fee);

        // if amount is null, we should use the amount from the feeType
        if (is_null($fee->getNetAmount())) {
            $fee->setNetAmount($feeType->getAmount());
            $fee->setVatandGrossAmountsFromNetAmountUsingRate($feeType->getVatRate());
        }

        // if amount is 0 set the status to paid
        if (empty($fee->getNetAmount())) {
            $fee->setFeeStatus($this->getRepo()->getRefdataReference(Fee::STATUS_PAID));
        }

        return $fee;
    }

    /**
     * @param Cmd $command
     * @param Fee $fee
     * @return null
     */
    private function handleTaskLink($command, $fee)
    {
        if ($command->getTask() !== null) {
            $fee->setTask($this->getRepo()->getReference(Task::class, $command->getTask()));
        }
    }

    /**
     * @param Cmd $command
     * @param Fee $fee
     * @return null
     */
    private function handleLicenceLink($command, $fee)
    {
        if ($command->getLicence() !== null) {
            $fee->setLicence($this->getRepo()->getReference(Licence::class, $command->getLicence()));
        }
    }

    /**
     * @param Cmd $command
     * @param Fee $fee
     * @return null
     */
    private function handleIrfoGvPermitLink($command, $fee)
    {
        if ($command->getIrfoGvPermit() !== null) {
            $fee->setIrfoGvPermit($this->getRepo()->getReference(IrfoGvPermit::class, $command->getIrfoGvPermit()));
        }
    }

    /**
     * @param Cmd $command
     * @param Fee $fee
     * @return null
     */
    private function handleIrfoPsvAuthLink($command, $fee)
    {
        if ($command->getIrfoPsvAuth() !== null) {
            $fee->setIrfoPsvAuth($this->getRepo()->getReference(IrfoPsvAuth::class, $command->getIrfoPsvAuth()));
        }
    }

    /**
     * @param Cmd $command
     * @param Fee $fee
     * @return null
     */
    private function handleApplicationLink($command, $fee)
    {
        if ($command->getApplication() !== null) {
            $application = $this->getRepo()->getReference(Application::class, $command->getApplication());
            $fee->setApplication($application);
            if (!$fee->getLicence()) {
                // if licence id wasn't specified, link the application's licence
                $fee->setLicence($application->getLicence());
            }
        }
    }

    /**
     * @param Cmd $command
     * @param Fee $fee
     * @return null
     */
    private function handleBusRegLink($command, $fee)
    {
        if ($command->getBusReg() !== null) {
            $busReg = $this->getRepo()->getReference(BusReg::class, $command->getBusReg());
            $fee->setBusReg($busReg);
            if (!$fee->getLicence()) {
                // if licence id wasn't specified, link the bus reg's licence
                $fee->setLicence($busReg->getLicence());
            }
        }
    }

    /**
     * @param Cmd $command
     * @throws ValidationException
     * @return bool
     */
    public function validate(Cmd $command, $feeType)
    {
        if ($feeType->isMiscellaneous()) {
            // misc fees don't need linked entities
            return true;
        }

        if ($feeType->isAdjustment()) {
            // balancing/overpayment fees don't need linked entities
            return true;
        }

        $this->validateLinkedEntity($command);

        if ($command->getIrfoGvPermit() && $command->getIrfoPsvAuth()) {
            $msg = 'Fee must be linked to a either GV Permit or PSV Authorisation but not both';
            throw new ValidationException(
                [
                    'irfoGvPermit' => [$msg],
                    'irfoPsvAuth' => [$msg],
                ]
            );
        }

        return true;
    }

    /**
     * @throws ValidationException
     */
    private function validateLinkedEntity(Cmd $command)
    {
        if (empty($command->getLicence())
            && empty($command->getApplication())
            && empty($command->getBusReg())
            && empty($command->getTask())
            && empty($command->getIrfoGvPermit())
            && empty($command->getIrfoPsvAuth())
        ) {
            throw new ValidationException(['Fee must be linked to an entity']);
        }
    }
}
