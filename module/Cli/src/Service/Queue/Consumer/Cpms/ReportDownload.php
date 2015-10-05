<?php

/**
 * Cpms Report Download Queue Consumer
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Cli\Service\Queue\Consumer\Cpms;

// use Dvsa\Olcs\Api\Domain\Command\Cpms\ReportStatus as Cmd;
use Dvsa\Olcs\Api\Domain\Exception\Exception as DomainException;
use Dvsa\Olcs\Api\Domain\Exception\NotReadyException;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\AbstractConsumer;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\MessageConsumerInterface;
use Dvsa\Olcs\Transfer\Query\Cpms\ReportStatus as ReportStatusQry;
use Dvsa\Olcs\Transfer\Command\Cpms\DownloadReport as DownloadReportCmd;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Queue\Complete as CompleteCmd;
use Dvsa\Olcs\Api\Domain\Command\Queue\Failed as FailedCmd;
use Dvsa\Olcs\Api\Domain\Command\Queue\Retry as RetryCmd;


/**
 * Cpms Report Download Queue Consumer
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ReportDownload implements MessageConsumerInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Process the message item
     *
     * @param QueueEntity $item
     * @return string
     */
    public function processMessage(QueueEntity $item)
    {
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
            'Download using reference %s and token %s, extension %s',
            [$reference, $result['token'], $result['extension']]
        );

        $command = DownloadReportCmd::create(
            [
                'reference' => $reference,
                'token' => $result['token']
            ]
        );
        $downloadResult = $this->getServiceLocator()->get('CommandHandlerManager')->handleCommand($command);

        $messages = array_merge([$msg], $downloadResult->getMessages());

        return $this->success($item, implode(', ', $messages));
    }

    /**
     * Called when processing the message was successful
     *
     * @param QueueEntity $item
     * @return string
     */
    protected function success(QueueEntity $item, $message = null)
    {
        $command = CompleteCmd::create(['item' => $item]);
        $this->getServiceLocator()->get('CommandHandlerManager')
            ->handleCommand($command);

        return 'Successfully processed message: '
            . $item->getId() . ' ' . $item->getOptions()
            . ($message ? ' ' . $message : '');
    }

    /**
     * Requeue the message
     *
     * @param QueueEntity $item
     * @param string $retryAfter (seconds)
     * @return string
     */
    protected function retry(QueueEntity $item, $retryAfter)
    {
        $command = RetryCmd::create(['item' => $item, 'retryAfter' => $retryAfter]);
        $this->getServiceLocator()->get('CommandHandlerManager')
            ->handleCommand($command);

        return 'Requeued message: '
            . $item->getId() . ' ' . $item->getOptions()
            . ' for retry in ' .  $retryAfter;
    }

    /**
     * Mark the message as failed
     *
     * @param QueueEntity $item
     * @param string $reason
     * @return string
     */
    protected function failed(QueueEntity $item, $reason = null)
    {
        var_dump($reason);
    }
}
