<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\ContinuationDetail;

use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Transfer\Command\ContinuationDetail\Process as Command;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;

/**
 * Process ContinuationDetail
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 * @author Mat Evans <mat.evans@valtech.co.uk> (original Business Service)
 */
final class Process extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'ContinuationDetail';

    /**
     * @todo migrate logic from olcs-internal
     * @see Cli\BusinessService\Service\ContinuationDetail
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();
        return $result;
    }
}
