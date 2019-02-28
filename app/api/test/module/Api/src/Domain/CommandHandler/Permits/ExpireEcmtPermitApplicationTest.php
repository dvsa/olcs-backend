<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpPermit;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\AbstractUpdateDefinedValueTest;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\ExpireEcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;

class ExpireEcmtPermitApplicationTest extends AbstractUpdateDefinedValueTest
{
    protected $repoServiceName = 'EcmtPermitApplication';
    protected $entityMethodName = 'expire';
    protected $definedValue = EcmtPermitApplication::STATUS_EXPIRED;
    protected $entityClass = EcmtPermitApplication::class;
    protected $sutClass = ExpireEcmtPermitApplication::class;
}
