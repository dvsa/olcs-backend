<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepository;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\Common\ArrayCollectionFactory;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm\RestrictedCountriesAnswerClearer;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\GenericAnswerClearer;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * RestrictedCountriesAnswerClearerTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class RestrictedCountriesAnswerClearerTest extends MockeryTestCase
{
    public function testClear()
    {
        $emptyArrayCollection = m::mock(ArrayCollection::class);

        $applicationStep = m::mock(ApplicationStepEntity::class);

        $irhpApplication = m::mock(IrhpApplicationEntity::class);
        $irhpApplication->shouldReceive('updateCountries')
            ->with($emptyArrayCollection)
            ->once()
            ->globally()
            ->ordered();

        $genericAnswerClearer = m::mock(GenericAnswerClearer::class);
        $genericAnswerClearer->shouldReceive('clear')
            ->with($applicationStep, $irhpApplication)
            ->once();

        $irhpApplicationRepo = m::mock(IrhpApplicationRepository::class);
        $irhpApplicationRepo->shouldReceive('save')
            ->with($irhpApplication)
            ->once()
            ->globally()
            ->ordered();

        $arrayCollectionFactory = m::mock(ArrayCollectionFactory::class);
        $arrayCollectionFactory->shouldReceive('create')
            ->withNoArgs()
            ->andReturn($emptyArrayCollection);

        $restrictedCountriesAnswerClearer = new RestrictedCountriesAnswerClearer(
            $genericAnswerClearer,
            $irhpApplicationRepo,
            $arrayCollectionFactory
        );

        $restrictedCountriesAnswerClearer->clear($applicationStep, $irhpApplication);
    }
}
