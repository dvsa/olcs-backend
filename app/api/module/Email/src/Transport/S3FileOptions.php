<?php
namespace Dvsa\Olcs\Email\Transport;

use Zend\Stdlib\AbstractOptions;

/**
 * Class S3FileOptions
 */
class S3FileOptions extends AbstractOptions
{
    /** @var string */
    protected $s3Path;

    /**
     * Set the S3 path
     *
     * @param string $path bucket/path
     *
     * @return void
     */
    public function setS3Path($path)
    {
        $this->s3Path = $path;
    }

    /**
     * Get the S3 path
     *
     * @return string
     */
    public function getS3Path()
    {
        return $this->s3Path;
    }
}
