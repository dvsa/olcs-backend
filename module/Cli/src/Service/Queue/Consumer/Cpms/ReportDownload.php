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
        } catch (NotReadyException $e) {
            return $this->retry($item, $e->getRetryAfter());
        } catch (DomainException $e) {
            $message = !empty($e->getMessages()) ? implode(', ', $e->getMessages()) : $e->getMessage();
            return $this->failed($item, $message);
        } catch (\Exception $e) {
            return $this->failed($item, $e->getMessage());
        }

        $msg = vsprintf(
            'Download using reference %s and token %s and extension %s',
            [$reference, $result['token'], $result['extension']]
        );

        $extension = $result['extension'] ? ('.'.$result['extension']) : '';
        $filename = 'Daily Balance Report' . $extension;
        $command = DownloadReportCmd::create(
            [
                'reference' => $reference,
                'token'     => $result['token'],
                'filename'  => $filename,
            ]
        );
        $downloadResult = $this->getServiceLocator()->get('CommandHandlerManager')->handleCommand($command);

        $messages = array_merge([$msg], $downloadResult->getMessages());

        return $this->success($item, implode('|', $messages));
    }
}
