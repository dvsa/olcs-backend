<?php

/**
 * Generate Licence Number
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command\Application;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Generate Licence Number
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class GenerateLicenceNumber extends AbstractCommand
{
    protected $id;

    public function getId()
    {
        return $this->id;
    }
}
