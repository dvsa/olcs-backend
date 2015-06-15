<?php

/**
 * Update Fee
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Fee;

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

        // @TODO if fee paid
        // $result->merge(
        //     $this->getCommandHandler()->handleCommand(PayFeeCmd::create(['id' => $fee->getId()]))
        // );

        return $result;
    }
}
