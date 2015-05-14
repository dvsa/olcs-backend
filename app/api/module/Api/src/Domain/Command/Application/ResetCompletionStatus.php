<?php

/**
 * Reset Completion Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command\Application;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Reset Completion Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class ResetCompletionStatus extends AbstractCommand
{
    protected $id;

    public function getId()
    {
        return $this->id;
    }
}
