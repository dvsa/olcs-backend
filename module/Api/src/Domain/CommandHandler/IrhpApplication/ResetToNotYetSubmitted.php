<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractUpdateDefinedValue;
use Dvsa\Olcs\Api\Entity\IrhpInterface;

/**
 * Reset to NotYetSubmitted
 */
final class ResetToNotYetSubmitted extends AbstractUpdateDefinedValue
{
    protected $repoServiceName = 'IrhpApplication';
    protected $entityMethodName = 'resetToNotYetSubmitted';
    protected $definedValue = IrhpInterface::STATUS_NOT_YET_SUBMITTED;
    protected $isRefData = true;
}
