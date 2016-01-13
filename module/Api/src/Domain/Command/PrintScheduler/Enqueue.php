<?php

/**
 * Enqueue a print job
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command\PrintScheduler;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Enqueue a print job
 */
final class Enqueue extends AbstractCommand
{
    protected $fileIdentifier;

    protected $jobName;

    /**
     * Get file identifier
     *
     * @return string
     */
    public function getFileIdentifier()
    {
        return $this->fileIdentifier;
    }

    /**
     * Get job name
     *
     * @return string
     */
    public function getJobName()
    {
        return $this->jobName;
    }
}
