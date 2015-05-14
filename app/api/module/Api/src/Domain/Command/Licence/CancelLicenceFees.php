<?php

/**
 * Cancel Licence Fees
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command\Licence;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Cancel Licence Fees
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CancelLicenceFees extends AbstractCommand
{
    protected $id;

    public function getId()
    {
        return $this->id;
    }
}
