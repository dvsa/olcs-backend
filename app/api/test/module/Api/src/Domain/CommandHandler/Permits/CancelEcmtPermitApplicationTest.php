<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\CancelEcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication as EcmtPermitApplicationEntity;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\AbstractCancelApplicationHandlerTest;

class CancelEcmtPermitApplicationTest extends AbstractCancelApplicationHandlerTest
{
    protected $repoServiceName = 'EcmtPermitApplication';
    protected $entityClass = EcmtPermitApplicationEntity::class;
    protected $sutClass = CancelEcmtPermitApplication::class;
    protected $cancelStatus = IrhpInterface::STATUS_CANCELLED;
}
