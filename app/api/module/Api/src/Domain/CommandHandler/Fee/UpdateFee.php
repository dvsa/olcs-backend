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
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Update Fee
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class UpdateFee extends AbstractCommandHandler
{
    protected $repoServiceName = 'Fee';

    /**
     * @todo implement fee payment side effects
     * @see Common\Service\Listener\FeeListenerService
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $fee = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $fee->setFeeStatus($this->getRepo()->getRefdataReference($command->getStatus()));

        if (!is_null($command->getWaiveReason())) {
            $fee->setWaiveReason($command->getWaiveReason());
        }

        $this->getRepo()->save($fee);

        // @TODO if fee paid
        // $result->merge(
        //     $this->getCommandHandler()->handleCommand(PayFeeCmd::create(['id' => $fee->getId()]))
        // );

        $result = new Result();

        $result->addId('fee', $fee->getId());
        $result->addMessage('Fee updated');

        return $result;
    }
}
