<?php

/**
 * Allocate IRHP Permit Application Permit
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command\Permits;

use Dvsa\Olcs\Transfer\FieldType\Traits\Identity;
use Dvsa\Olcs\Transfer\Command\AbstractCommand;

class AllocateIrhpPermitApplicationPermit extends AbstractCommand
{
    use Identity;
}
