<?php

/**
 * Update TaxiPhv Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Update TaxiPhv Status
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateTaxiPhvStatus extends AbstractCommand
{
    protected $id;

    public function getId()
    {
        return $this->id;
    }
}
