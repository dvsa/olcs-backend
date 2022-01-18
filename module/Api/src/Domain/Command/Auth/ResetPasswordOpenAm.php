<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\Command\Auth;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;
use Dvsa\Olcs\Transfer\FieldType\Traits\ConfirmationId;
use Dvsa\Olcs\Transfer\FieldType\Traits\Password;
use Dvsa\Olcs\Transfer\FieldType\Traits\Realm;
use Dvsa\Olcs\Transfer\FieldType\Traits\TokenId;
use Dvsa\Olcs\Transfer\FieldType\Traits\Username;

class ResetPasswordOpenAm extends AbstractCommand
{
    use Username;
    use Password;
    use Realm;
    use ConfirmationId;
    use TokenId;
}
