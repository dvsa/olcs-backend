<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\CandidatePermits;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange;
use Dvsa\Olcs\Api\Service\Permits\CandidatePermits\ApggCandidatePermitFactory;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * ApggCandidatePermitFactoryTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ApggCandidatePermitFactoryTest extends MockeryTestCase
{
    public function testCreate()
    {
        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitRange = m::mock(IrhpPermitRange::class);

        $apggCandidatePermitFactory = new ApggCandidatePermitFactory();
        $irhpCandidatePermit = $apggCandidatePermitFactory->create($irhpPermitApplication, $irhpPermitRange);

        $this->assertSame($irhpPermitApplication, $irhpCandidatePermit->getIrhpPermitApplication());
        $this->assertSame($irhpPermitRange, $irhpCandidatePermit->getIrhpPermitRange());
        $this->assertEquals(1, $irhpCandidatePermit->getSuccessful());
    }
}
