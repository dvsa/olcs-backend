<?php

/**
 * End IRHP applications and permits relating to a licence
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command\Licence;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;
use Dvsa\Olcs\Transfer\FieldType\Traits\Identity;
use Dvsa\Olcs\Transfer\FieldType\Traits\PermitAppWithdrawReason;

/**
 * End IRHP applications and permits relating to a licence
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
final class EndIrhpApplicationsAndPermits extends AbstractCommand
{
    use Identity, PermitAppWithdrawReason;
}
