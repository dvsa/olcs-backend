<?php

namespace Dvsa\Olcs\Email\Transport;

use Zend\Stdlib\AbstractOptions;
use Aws\S3\S3Client;

/**
 * Class S3FileOptions
 *
 * @codeCoverageIgnore
 */
class S3FileOptions extends AbstractOptions
{
    /** @var string */
    protected $s3Bucket;

    /**
     * @var string
     */
    protected $s3Key;

    /**
     * @var string
     */
    protected $awsOptions;

    /**
     * @var string
     */
    protected $s3Options;

    /**
     * @var S3Client
     */
    protected $s3Client;



    /**
     * S3FileOptions constructor.
     *
     * @param $options
     * @param $s3Client
     */
    public function __construct($options, $s3Client)
    {
        $this->s3Client = $s3Client;
        parent::__construct($options);
    }

    /**
     * @return S3Client
     */
    public function getS3Client(): S3Client
    {
        return $this->s3Client;
    }


    /**
     * Set the S3 path
     *
     * @param string $path bucket/path
     *
     * @return void
     */
    public function setS3Bucket($path)
    {
        $this->s3Bucket = $path;
    }

    /**
     * Get the S3 path
     *
     * @return string
     */
    public function getS3Bucket()
    {
        return $this->s3Bucket;
    }

    /**
     * @return mixed
     */
    public function getAwsOptions()
    {
        return $this->awsOptions;
    }

    /**
     * @param mixed $awsOptions
     */
    public function setAwsOptions($awsOptions): void
    {
        $this->awsOptions = $awsOptions;
    }

    /**
     * @return mixed
     */
    public function getS3Options()
    {
        return $this->s3Options;
    }

    /**
     * @param mixed $s3Options
     */
    public function setS3Options($s3Options): void
    {
        $this->s3Options = $s3Options;
    }

    /**
     * @return string
     */
    public function getS3Key(): string
    {
        return $this->s3Key;
    }

    /**
     * @param string $s3Key
     */
    public function setS3Key(string $s3Key): void
    {
        $this->s3Key = $s3Key;
    }
}
