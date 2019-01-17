<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\AbstractUpdateCommandHandlerTest;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\UpdateEcmtTrips;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;

class UpdateEcmtTripsTest extends AbstractUpdateCommandHandlerTest
{
    protected $repoServiceName = 'EcmtPermitApplication';
    protected $commandMethodName = 'getEcmtTrips';
    protected $entityMethodName = 'updateTrips';
    protected $entityClass = EcmtPermitApplication::class;
    protected $sutClass = UpdateEcmtTrips::class;
}
