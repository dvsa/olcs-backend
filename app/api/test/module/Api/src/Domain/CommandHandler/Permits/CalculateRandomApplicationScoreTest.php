<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtAppSubmitted;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\EcmtSubmitApplication;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Mockery as m;

class CalculateRandomApplicationScore extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->mockRepo('IrhpPermitStock', EcmtPermitStock::class);
        $this->mockRepo('IrhpPermitApplication', IrhpPermitApplication::class);
        $this->mockRepo('IrhpCandidatePermit', IrhpCandidatePermit::class);

        $this->sut = new EcmtPermitStock();

        parent::setUp();
    }
}
