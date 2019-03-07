<?php

/**
 * Expire ECMT Permit Application
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
namespace Dvsa\Olcs\Api\Domain\Command\Permits;


use Dvsa\Olcs\Transfer\FieldType\Traits\Identity;
use Dvsa\Olcs\Transfer\Command\AbstractCommand;

final class ExpireEcmtPermitApplication extends AbstractCommand
{
    use Identity;
}
