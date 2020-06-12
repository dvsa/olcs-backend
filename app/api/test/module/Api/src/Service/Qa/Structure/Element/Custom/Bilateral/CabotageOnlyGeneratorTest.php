<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral\CabotageOnly;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral\CabotageOnlyFactory;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral\CabotageOnlyGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * CabotageOnlyGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class CabotageOnlyGeneratorTest extends MockeryTestCase
{
    /**
     * @dataProvider dpGenerate
     */
    public function testGenerate($answerValue, $expectedYesNo)
    {
        $countryName = 'Germany';

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock->getCountry->getCountryDesc')
            ->withNoArgs()
            ->andReturn($countryName);

        $elementGeneratorContext = m::mock(ElementGeneratorContext::class);
        $elementGeneratorContext->shouldReceive('getAnswerValue')
            ->withNoArgs()
            ->andReturn($answerValue);
        $elementGeneratorContext->shouldReceive('getQaEntity')
            ->withNoArgs()
            ->andReturn($irhpPermitApplication);

        $cabotageOnly = m::mock(CabotageOnly::class);

        $cabotageOnlyFactory = m::mock(CabotageOnlyFactory::class);
        $cabotageOnlyFactory->shouldReceive('create')
            ->with($expectedYesNo, $countryName)
            ->andReturn($cabotageOnly);

        $cabotageOnlyGenerator = new CabotageOnlyGenerator($cabotageOnlyFactory);

        $this->assertSame(
            $cabotageOnly,
            $cabotageOnlyGenerator->generate($elementGeneratorContext)
        );
    }

    public function dpGenerate()
    {
        return [
            ['string_value', true],
            [null, null],
        ];
    }
}
