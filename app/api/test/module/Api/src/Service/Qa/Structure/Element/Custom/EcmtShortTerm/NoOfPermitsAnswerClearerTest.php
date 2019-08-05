<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitApplication as IrhpPermitApplicationRepository;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication as IrhpPermitApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm\NoOfPermitsAnswerClearer;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * NoOfPermitsAnswerClearerTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class NoOfPermitsAnswerClearerTest extends MockeryTestCase
{
    public function testClear()
    {
        $applicationStep = m::mock(ApplicationStepEntity::class);

        $irhpPermitApplication = m::mock(IrhpPermitApplicationEntity::class);
        $irhpPermitApplication->shouldReceive('clearEmissionsCategoryPermitsRequired')
            ->once()
            ->withNoArgs()
            ->ordered()
            ->globally();

        $irhpApplication = m::mock(IrhpApplicationEntity::class);
        $irhpApplication->shouldReceive('getFirstIrhpPermitApplication')
            ->andReturn($irhpPermitApplication);

        $irhpPermitApplicationRepo = m::mock(IrhpPermitApplicationRepository::class);
        $irhpPermitApplicationRepo->shouldReceive('save')
            ->with($irhpPermitApplication)
            ->once()
            ->ordered()
            ->globally();

        $noOfPermitsAnswerClearer = new NoOfPermitsAnswerClearer($irhpPermitApplicationRepo);

        $noOfPermitsAnswerClearer->clear($applicationStep, $irhpApplication);
    }
}
