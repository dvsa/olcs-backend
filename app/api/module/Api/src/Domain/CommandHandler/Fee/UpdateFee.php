<?php

/**
 * Update Fee
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Fee;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Fee\PayFee as PayFeeCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;

/**
 * Update Fee
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class UpdateFee extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Fee';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var FeeEntity $fee */
        $fee = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $fee->setFeeStatus($this->getRepo()->getRefdataReference($command->getStatus()));

        if (!is_null($command->getWaiveReason())) {
            $fee->setWaiveReason($command->getWaiveReason());
        }

        $this->getRepo()->save($fee);

        if (in_array($fee->getFeeStatus()->getId(), [FeeEntity::STATUS_WAIVED, FeeEntity::STATUS_PAID])) {
            $result->merge(
                $this->getCommandHandler()->handleCommand(PayFeeCmd::create(['id' => $fee->getId()]))
            );
        }

        $result->addId('fee', $fee->getId());
        $result->addMessage('Fee updated');

        return $result;
    }
}
