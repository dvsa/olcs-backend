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
    protected $documentId;

    protected $jobName;

    protected $user;

    /**
     * Get the document ID
     *
     * @return int
     */
    public function getDocumentId()
    {
        return $this->documentId;
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

    /**
     * Get user id
     *
     * @return int
     */
    public function getUser()
    {
        return $this->user;
    }
}
