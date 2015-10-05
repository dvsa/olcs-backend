<?php

/**
 * Request Cpms Report
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cpms;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CpmsAwareInterface;
use Dvsa\Olcs\Api\Domain\CpmsAwareTrait;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Cpms\RequestReport as Cmd;

/**
 * Request Cpms Report
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class RequestReport extends AbstractCommandHandler implements CpmsAwareInterface
{
    use CpmsAwareTrait;

    /**
     * @param Cmd $command
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     * @todo we may want to pass in start/end as command params
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $start = new \DateTime($command->getStart());
        $end = new \DateTime($command->getEnd());

        $data = $this->getCpmsService()->requestReport($command->getReportCode(), $start, $end);

        // @todo we'll probably stick the reference number on a queue then return the queue
        // message id. For now, just return the reference.

        $result->addMessage('Report requested');
        $result->addId('cpmsReport', $data['reference']);

        return $result;
    }
}
