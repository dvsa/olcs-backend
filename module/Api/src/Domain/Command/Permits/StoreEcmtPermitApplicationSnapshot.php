<?php

namespace Dvsa\Olcs\Api\Domain\Command\Permits;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;
use Dvsa\Olcs\Transfer\FieldType\Traits\Identity;

final class StoreEcmtPermitApplicationSnapshot extends AbstractCommand
{
    use Identity;
}
