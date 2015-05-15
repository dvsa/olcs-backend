<?php

/**
 * Update Safety Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Update Safety Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateSafetyStatus extends AbstractCommand
{
    protected $id;

    public function getId()
    {
        return $this->id;
    }
}
