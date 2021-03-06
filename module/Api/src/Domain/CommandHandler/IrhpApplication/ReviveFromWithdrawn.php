<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractUpdateDefinedValue;
use Dvsa\Olcs\Api\Entity\IrhpInterface;

/**
 * Revive IRHP Application from withdrawn state
 */
final class ReviveFromWithdrawn extends AbstractUpdateDefinedValue
{
    protected $repoServiceName = 'IrhpApplication';
    protected $entityMethodName = 'reviveFromWithdrawn';
    protected $definedValue = IrhpInterface::STATUS_UNDER_CONSIDERATION;
    protected $isRefData = true;
}
