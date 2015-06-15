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
final class CreateFee extends AbstractCommandHandler
{
    protected $repoServiceName = 'Fee';

    /**
     * @todo implement fee payment side effects
     * @see Common\Service\Listener\FeeListenerService
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();
        return $result;
    }
}
