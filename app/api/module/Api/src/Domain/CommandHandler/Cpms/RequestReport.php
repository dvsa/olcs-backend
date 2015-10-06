<?php

/**
 * Request Cpms Report
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cpms;

use Dvsa\Olcs\Api\Domain\Command\Queue\Create as CreateQueueCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CpmsAwareInterface;
use Dvsa\Olcs\Api\Domain\CpmsAwareTrait;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Cpms\RequestReport as Cmd;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;

/**
 * Request Cpms Report
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class RequestReport extends AbstractCommandHandler implements AuthAwareInterface, CpmsAwareInterface
{
    use CpmsAwareTrait,
        AuthAwareTrait;

    /**
     * @param Cmd $command
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $start = new \DateTime($command->getStart());
        $end = new \DateTime($command->getEnd());

        $data = $this->getCpmsService()->requestReport($command->getReportCode(), $start, $end);

        $queueCmd = CreateQueueCmd::create(
            [
                'type' => Queue::TYPE_CPMS_REPORT_DOWNLOAD,
                'status' => Queue::STATUS_QUEUED,
                'user' => $this->getCurrentUser()->getId(),
                'options' => json_encode(
                    [
                        'reference' => $data['reference'],
                    ]
                ),
            ]
        );
        $result->merge($this->handleSideEffect($queueCmd));

        $result->addMessage('Report requested');
        $result->addId('cpmsReport', $data['reference']);

        return $result;
    }
}
