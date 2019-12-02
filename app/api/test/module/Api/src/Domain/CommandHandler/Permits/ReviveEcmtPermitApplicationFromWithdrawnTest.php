<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\ReviveEcmtPermitApplicationFromWithdrawn;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\AbstractUpdateDefinedValueTest;

class ReviveEcmtPermitApplicationFromWithdrawnTest extends AbstractUpdateDefinedValueTest
{
    protected $repoServiceName = 'EcmtPermitApplication';
    protected $entityMethodName = 'reviveFromWithdrawn';
    protected $definedValue = IrhpInterface::STATUS_UNDER_CONSIDERATION;
    protected $entityClass = EcmtPermitApplication::class;
    protected $sutClass = ReviveEcmtPermitApplicationFromWithdrawn::class;
}
