<?php

/**
 * Update ConvictionsPenalties Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Update ConvictionsPenalties Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateConvictionsPenaltiesStatus extends AbstractCommand
{
    protected $id;

    public function getId()
    {
        return $this->id;
    }
}
