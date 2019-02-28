<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpPermit;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\AbstractUpdateDefinedValueTest;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermit\Terminate;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit;

class TerminateTest extends AbstractUpdateDefinedValueTest
{
    protected $repoServiceName = 'IrhpPermit';
    protected $entityMethodName = 'proceedToStatus';
    protected $definedValue = IrhpPermit::STATUS_TERMINATED;
    protected $entityClass = IrhpPermit::class;
    protected $sutClass = Terminate::class;
}
