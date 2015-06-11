<?php

/**
 * Enqueue File
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command\PrintScheduler;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Enqueue File
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class EnqueueFile extends AbstractCommand
{
    public $fileId;

    public $jobName;

    public $options;
    
    public function getFileId()
    {
        return $this->fileId;
    }

    public function getJobName()
    {
        return $this->jobName;
    }

    public function getOptions()
    {
        return $this->options;
    }
}
