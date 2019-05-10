<?php

namespace Dvsa\Olcs\Email\Transport;

use Aws\S3\Exception\S3Exception;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Zend\I18n\Filter\Alnum;
use Zend\Mail\Transport\Exception\RuntimeException;
use Zend\Mail\Message;
use Zend\Mail\Transport\File;
use Zend\Mail\Transport\TransportInterface;

/**
 * Class S3File
 */
class S3File implements TransportInterface
{
    /**
     * @var S3FileOptions
     */
    protected $options;

    /**
     * @var File
     */
    private $fileTransport;

    /**
     * S3File constructor.
     *
     * @param File|null $fileTransport File Transport to use for creating the file
     */
    public function __construct(File $fileTransport = null)
    {
        if ($fileTransport === null) {
            $fileTransport = new File();
        }
        $this->fileTransport = $fileTransport;
    }

    /**
     * Sets options
     *
     * @param S3FileOptions $options Options
     *
     * @return void
     */
    public function setOptions(S3FileOptions $options)
    {
        $this->options = $options;
    }

    /**
     * Get options
     *
     * @return S3FileOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Saves e-mail message to a file and upload it to S3
     *
     * @param Message $message Email message
     *
     * @return void
     */
    public function send(Message $message)
    {
        $this->fileTransport->send($message);
        $file = $this->fileTransport->getLastFile();

        $filter = new Alnum(true);
        $s3FileName = substr(str_replace(' ', '_', $filter->filter($message->getSubject())), 0, 100);

        $s3Client = $this->getOptions()->getS3Client();
        //['s3Path' => 'devapp-olcs-pri-olcs-autotest-s3/olcs.qa.nonprod.dvsa.aws/email']],

        $bucket = $this->getOptions()->getS3Bucket();
        $key = $this->getOptions()->getS3Key();

        try{
            $result = $s3Client->putObject([
                'Bucket' => $bucket,
                'Key' => $key,
                'SourceFile' => $s3FileName,
            ]);

        }
        catch (S3Exception $e) {
            $this->deleteFile($file); //clean up email file
            throw new RuntimeException('Cannot send mail to S3 : '. $e->getAwsErrorMessage() );
        }

        $this->deleteFile($file);

    }

    /**
     * Execute a system command
     *
     * @param string $command CLI command to execute
     * @param array  &$output Output from command
     * @param int    &$result Result/exit code
     *
     * @return void
     * @codeCoverageIgnore
     */
    protected function executeCommand($command, &$output, &$result)
    {
        exec($command, $output, $result);
    }

    /**
     * Delete a file
     *
     * @param string $file File to delete
     *
     * @return void
     */
    protected function deleteFile($file)
    {
        unlink($file);
    }
}
