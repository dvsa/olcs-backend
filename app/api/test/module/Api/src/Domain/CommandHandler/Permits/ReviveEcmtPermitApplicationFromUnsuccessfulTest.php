<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\ReviveEcmtPermitApplicationFromUnsuccessful;
use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication as EcmtPermitApplicationRepo;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpApplication\AbstractReviveFromUnsuccessfulTest;

class ReviveEcmtPermitApplicationFromUnsuccessfulTest extends AbstractReviveFromUnsuccessfulTest
{
    protected $applicationRepoServiceName = 'EcmtPermitApplication';
    protected $applicationRepoClass = EcmtPermitApplicationRepo::class;
    protected $sutClass = ReviveEcmtPermitApplicationFromUnsuccessful::class;
    protected $entityClass = EcmtPermitApplication::class;
}
