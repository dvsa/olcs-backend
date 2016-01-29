<?php

/**
 * Print Job
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command\PrintScheduler;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;
use Dvsa\Olcs\Transfer\FieldType\Traits\Identity;

/**
 * Print Job
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class PrintJob extends AbstractCommand
{
    use Identity;

    protected $document;

    protected $title;

    protected $user;

    public function getDocument()
    {
        return $this->document;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getUser()
    {
        return $this->user;
    }
}
