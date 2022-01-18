<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\Command\Auth;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;
use Dvsa\Olcs\Transfer\FieldType\Traits\Realm;
use Dvsa\Olcs\Transfer\FieldType\Traits\Username;

class ForgotPasswordOpenAm extends AbstractCommand
{
    use Username;
    use Realm;
}
