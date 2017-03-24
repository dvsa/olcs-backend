<?php

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

    protected $isDiscPrinting = false;

    /** @var int */
    protected $copies;

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

    /**
     * Get is a disc printing job
     *
     * @return bool
     */
    public function getIsDiscPrinting()
    {
        return $this->isDiscPrinting;
    }

    /**
     * Get count of copies
     *
     * @return int
     */
    public function getCopies()
    {
        return $this->copies;
    }
}
