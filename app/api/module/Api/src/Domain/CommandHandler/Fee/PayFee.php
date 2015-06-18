<?php

/**
 * Pay Fee (handles fee side effects)
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Fee;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Pay Fee (handles fee side effects)
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class PayFee extends AbstractCommandHandler
{
    protected $repoServiceName = 'Fee';

    /**
     * @todo implement fee payment side effects
     * @see Common\Service\Listener\FeeListenerService
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function handleCommand(CommandInterface $command)
    {
        return new Result();
    }
}
