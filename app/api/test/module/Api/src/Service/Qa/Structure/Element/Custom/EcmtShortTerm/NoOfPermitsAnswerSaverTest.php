<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitApplication as IrhpPermitApplicationRepository;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication as IrhpPermitApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm\FieldNames;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm\NoOfPermitsAnswerFetcher;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm\NoOfPermitsAnswerSaver;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * NoOfPermitsAnswerSaverTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class NoOfPermitsAnswerSaverTest extends MockeryTestCase
{
    public function testSave()
    {
        $euro5Answer = '5';
        $euro6Answer = '27';

        $postData = [
            'fieldset68' => [
                'requiredEuro5' => '5',
                'requiredEuro6' => '27'
            ]
        ];

        $applicationStep = m::mock(ApplicationStepEntity::class);

        $irhpPermitApplication = m::mock(IrhpPermitApplicationEntity::class);
        $irhpPermitApplication->shouldReceive('updateEmissionsCategoryPermitsRequired')
            ->with($euro5Answer, $euro6Answer)
            ->once()
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

        $noOfPermitsAnswerFetcher = m::mock(NoOfPermitsAnswerFetcher::class);
        $noOfPermitsAnswerFetcher->shouldReceive('fetch')
            ->with($applicationStep, $postData, FieldNames::REQUIRED_EURO5)
            ->andReturn($euro5Answer);
        $noOfPermitsAnswerFetcher->shouldReceive('fetch')
            ->with($applicationStep, $postData, FieldNames::REQUIRED_EURO6)
            ->andReturn($euro6Answer);

        $noOfPermitsAnswerSaver = new NoOfPermitsAnswerSaver(
            $irhpPermitApplicationRepo,
            $noOfPermitsAnswerFetcher
        );

        $noOfPermitsAnswerSaver->save($applicationStep, $irhpApplication, $postData);
    }
}
