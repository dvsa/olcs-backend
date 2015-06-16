<?php

/**
 * Create Miscellaneous Fee
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Fee;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Fee\CreateMiscellaneousFee as Cmd;

/**
 * Create Miscellaneous Fee
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class CreateMiscellaneousFee extends AbstractCommandHandler
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

        if ($feeType->getIsMiscellaneous() !== true) {
            // only allow misc. fees to be create via this command
            throw new ValidationException(['Invalid fee type: ' . $command->getFeeType()]);
        }

        $feeStatus = $this->getRepo()->getRefdataReference(Fee::STATUS_OUTSTANDING);

        $fee = new Fee($feeType, $command->getAmount(), $feeStatus);

        if ($command->getInvoicedDate() !== null) {
            $fee->setInvoicedDate(new \DateTime($command->getInvoicedDate()));
        }

        $fee->setDescription($feeType->getDescription());


        $user = $this->getRepo()->getReference(User::class, $command->getUser());
        $fee->setCreatedBy($user); // @TODO do we need to explicitly do this?

        return $fee;
    }
}
