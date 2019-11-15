<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication\ReviveFromWithdrawn;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\AbstractUpdateDefinedValueTest;

class ReviveFromWithdrawnTest extends AbstractUpdateDefinedValueTest
{
    protected $repoServiceName = 'IrhpApplication';
    protected $entityMethodName = 'reviveFromWithdrawn';
    protected $definedValue = IrhpInterface::STATUS_UNDER_CONSIDERATION;
    protected $entityClass = IrhpApplication::class;
    protected $sutClass = ReviveFromWithdrawn::class;
}
