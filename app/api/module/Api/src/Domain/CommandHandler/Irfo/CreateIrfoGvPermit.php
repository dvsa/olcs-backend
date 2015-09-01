<?php

/**
 * Create Irfo Gv Permit
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Irfo;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermit;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermitType;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Transfer\Command\Irfo\CreateIrfoGvPermit as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee as FeeCreateFee;
use Dvsa\Olcs\Api\Entity\Fee\Fee;

/**
 * Create Irfo Gv Permit
 */
final class CreateIrfoGvPermit extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'IrfoGvPermit';

    /**
     * Tell the factory which repositories to lazy load
     */
    protected $extraRepos = [
        'FeeType'
    ];

    public function handleCommand(CommandInterface $command)
    {
        $irfoGvPermit = $this->createIrfoGvPermitObject($command);

        $this->getRepo()->save($irfoGvPermit);

        $result = new Result();
        $result->addId('irfoGvPermit', $irfoGvPermit->getId());
        $result->addMessage('IRFO GV Permit created successfully');

        // Check if is *not* fee exempt.
        if ($irfoGvPermit->getIsFeeExempt() !== 'Y') {
            $result->merge($this->createFee($irfoGvPermit));
        } else {
            $result->merge($this->createExemptFee($irfoGvPermit));
        }

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
        $status = $this->getRepo()->getRefdataReference(IrfoGvPermit::STATUS_PENDING);

        $irfoGvPermit = new IrfoGvPermit($organisation, $type, $status);

        $irfoGvPermit->setYearRequired($command->getYearRequired());
        $irfoGvPermit->setIsFeeExempt($command->getIsFeeExempt());
        $irfoGvPermit->setExemptionDetails($command->getExemptionDetails());
        $irfoGvPermit->setNoOfCopies($command->getNoOfCopies());

        $irfoGvPermit->setIrfoFeeId($this->getIrfoFeeId($organisation));

        if ($command->getInForceDate() !== null) {
            $irfoGvPermit->setInForceDate(new \DateTime($command->getInForceDate()));
        }

        return $irfoGvPermit;
    }

    public function getIrfoFeeId(Organisation $organisation)
    {
        return 'IR' . str_pad($organisation->getId(), 7, '0', STR_PAD_LEFT);
    }

    public function createFee(IrfoGvPermit $irfoGvPermit)
    {
        $irfoGvPermitFeeType = $irfoGvPermit->getIrfoGvPermitType()->getIrfoFeeType();

        /** @var \Dvsa\Olcs\Api\Domain\Repository\FeeType $feeTypeRepo */
        $feeTypeRepo = $this->getRepo('FeeType');
        $feeType = $feeTypeRepo->fetchLatestForIrfo($irfoGvPermitFeeType);

        $feeAmount = ((float)$feeType->getFixedValue() * (int)$irfoGvPermit->getNoOfCopies());

        $data = [
            'irfoGvPermit' => $irfoGvPermit->getId(),
            'invoicedDate' => date('Y-m-d'),
            'description' => $feeType->getDescription() . ' for IRFO permit ' . $irfoGvPermit->getId(),
            'feeType' => $feeType->getId(),
            'amount' => $feeAmount,
            'feeStatus' => Fee::STATUS_OUTSTANDING,
        ];

        return $this->handleSideEffect(FeeCreateFee::create($data));
    }

    public function createExemptFee(IrfoGvPermit $irfoGvPermit)
    {
        $irfoGvPermitFeeType = $irfoGvPermit->getIrfoGvPermitType()->getIrfoFeeType();

        /** @var \Dvsa\Olcs\Api\Domain\Repository\FeeType $feeTypeRepo */
        $feeTypeRepo = $this->getRepo('FeeType');
        $feeType = $feeTypeRepo->fetchLatestForIrfo($irfoGvPermitFeeType);

        $data = [
            'irfoGvPermit' => $irfoGvPermit->getId(),
            'invoicedDate' => date('Y-m-d'),
            'description' => $feeType->getDescription() . ' for IRFO permit ' . $irfoGvPermit->getId(),
            'feeType' => $feeType->getId(),
            'amount' => 0,
            'feeStatus' => Fee::STATUS_PAID,
        ];

        return $this->handleSideEffect(FeeCreateFee::create($data));
    }
}
