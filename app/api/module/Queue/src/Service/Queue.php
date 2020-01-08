<?php

namespace Dvsa\Olcs\Queue\Service;

use Aws\Exception\AwsException;
use Aws\Sqs\SqsClient;
use Olcs\Logging\Log\Logger;

class Queue
{
    /**
     * @var SqsClient
     */
    protected $sqsClient;

    const SEND_ACTION = 'send';
    const FETCH_ACTION = 'fetch';
    const DELETE_ACTION = 'delete';

    public function __construct(SqsClient $sqsClient)
    {
        $this->sqsClient = $sqsClient;
    }

    /**
     * Sends message to sqs queue and returns the response code
     *
     * @param array $message
     *
     */
    public function sendMessage(array $message): void
    {
        try {
            $this->sqsClient->sendMessage($message);
        } catch (AwsException $exception) {
            $this->logAwsException($exception, static::SEND_ACTION);
            throw $exception;
        }
    }

    /**
     *
     * @param string $queueUrl
     * @param int $maxMessages Number of messages to fetch. No more than 10
     *
     * @return array|null
     */
    public function fetchMessages(string $queueUrl, int $maxMessages): ?array
    {
        if ($maxMessages > 10) {
            throw new \InvalidArgumentException('maxMessages must be 10 or less');
        }

        try {
            $result = $this->sqsClient->receiveMessage([
                'MaxNumberOfMessages' => $maxMessages,
                'MessageAttributeNames' => ['All'],
                'QueueUrl' => $queueUrl,
                'WaitTimeSeconds' => 1,
                'VisibilityTimeout' => 1
            ]);

            return $result->get('Messages');
        } catch (AwsException $exception) {
            $this->logAwsException($exception, static::FETCH_ACTION);
            throw $exception;
        }
    }

    /**
     * @param string $queueUrl
     * @param string $receiptHandle
     * @return array
     */
    public function deleteMessage(string $queueUrl, string $receiptHandle): array
    {
        try {
            return $this->sqsClient->deleteMessage([
                'QueueUrl' => $queueUrl,
                'ReceiptHandle' => $receiptHandle
            ])->toArray();
        } catch (AwsException $exception) {
            $this->logAwsException($exception, static::DELETE_ACTION);
            throw $exception;
        }
    }

    /**
     * @param AwsException $exception
     * @param string $action
     * @return void
     */
    private function logAwsException(AwsException $exception, string $action): void
    {
        Logger::err(
            'Failed to ' . $action . ' message. Error code: ' .
            $exception->getAwsErrorCode() . ". Error msg: " .
            $exception->getAwsErrorMessage()
        );
    }
}
