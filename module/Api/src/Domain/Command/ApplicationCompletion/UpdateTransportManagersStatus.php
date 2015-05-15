<?php

/**
 * Update TransportManagers Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Update TransportManagers Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateTransportManagersStatus extends AbstractCommand
{
    protected $id;

    public function getId()
    {
        return $this->id;
    }
}
