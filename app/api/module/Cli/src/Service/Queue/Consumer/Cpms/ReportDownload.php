<?php

/**
 * Cpms Report Download Queue Consumer
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Cli\Service\Queue\Consumer\Cpms;

use Dvsa\Olcs\Api\Domain\Exception\Exception as DomainException;
use Dvsa\Olcs\Api\Domain\Exception\NotReadyException;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\AbstractConsumer;
use Dvsa\Olcs\Transfer\Command\Cpms\DownloadReport as DownloadReportCmd;
use Dvsa\Olcs\Transfer\Query\Cpms\ReportStatus as ReportStatusQry;

/**
 * Cpms Report Download Queue Consumer
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ReportDownload extends AbstractConsumer
{
    const MAX_ATTEMPTS = 10;

    /**
     * Process the message item
     *
     * @param QueueEntity $item
     * @return string
     */
    public function processMessage(QueueEntity $item)
    {
        if ($item->getAttempts() > self::MAX_ATTEMPTS) {
            return $this->failed($item, 'Maximum attempts exceeded');
        }

        $options = (array) json_decode($item->getOptions());
        $reference = $options['reference'];

        $query = ReportStatusQry::create(['reference' => $reference]);

        try {
            $result = $this->getServiceLocator()->get('QueryHandlerManager')->handleQuery($query);
        } catch (\Exception $e) {
            return $this->handleException($e, $item);
        }

        $msg = vsprintf(
            'Download using reference %s and token %s and extension %s',
            [$reference, $result['token'], $result['extension']]
        );

        $extension = $result['extension'] ? ('.'.$result['extension']) : '';
        $filename = $options['name'] . $extension;
        $command = DownloadReportCmd::create(
            [
                'reference' => $reference,
                'token'     => $result['token'],
                'filename'  => $filename,
                'user'      => $item->getCreatedBy()->getId()
            ]
        );
        try {
            $downloadResult = $this->getServiceLocator()->get('CommandHandlerManager')->handleCommand($command);
        } catch (\Exception $e) {
            return $this->handleException($e, $item);
        }

        $messages = array_merge([$msg], $downloadResult->getMessages());

        return $this->success($item, implode('|', $messages));
    }

    protected function handleException(\Exception $e, QueueEntity $item)
    {
        if ($e instanceof NotReadyException) {
            return $this->retry($item, $e->getRetryAfter());
        }

        if ($e instanceof DomainException) {
            $message = !empty($e->getMessages()) ? implode(', ', $e->getMessages()) : $e->getMessage();
            return $this->failed($item, $message);
        }

        return $this->failed($item, $e->getMessage());
    }
}
