<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm\RestrictedCountriesAnswerSummaryProvider;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * RestrictedCountriesAnswerSummaryProviderTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class RestrictedCountriesAnswerSummaryProviderTest extends MockeryTestCase
{
    private $applicationStepEntity;

    private $irhpApplicationEntity;

    private $restrictedCountriesAnswerSummaryProvider;

    public function setUp()
    {
        $this->applicationStepEntity = m::mock(ApplicationStepEntity::class);

        $this->irhpApplicationEntity = m::mock(IrhpApplicationEntity::class);

        $this->restrictedCountriesAnswerSummaryProvider = new RestrictedCountriesAnswerSummaryProvider();
    }

    public function testGetTemplateName()
    {
        $this->assertEquals(
            'ecmt-short-term-restricted-countries',
            $this->restrictedCountriesAnswerSummaryProvider->getTemplateName()
        );
    }

    /**
     * @dataProvider dpSnapshot
     */
    public function testGetTemplateVariablesWithCountries($isSnapshot)
    {
        $hasRestrictedCountries = true;
        $country1Description = 'Spain';
        $country2Description = 'Hungary';

        $country1 = m::mock(Country::class);
        $country1->shouldReceive('getCountryDesc')
            ->withNoArgs()
            ->andReturn($country1Description);

        $country2 = m::mock(Country::class);
        $country2->shouldReceive('getCountryDesc')
            ->withNoArgs()
            ->andReturn($country2Description);

        $countries = [$country1, $country2];

        $this->irhpApplicationEntity->shouldReceive('getAnswer')
            ->with($this->applicationStepEntity)
            ->andReturn($hasRestrictedCountries);
        $this->irhpApplicationEntity->shouldReceive('getCountrys')
            ->andReturn($countries);

        $expectedTemplateVariables = [
            'hasRestrictedCountries' => $hasRestrictedCountries,
            'countryNames' => [$country1Description, $country2Description]
        ];

        $templateVariables = $this->restrictedCountriesAnswerSummaryProvider->getTemplateVariables(
            $this->applicationStepEntity,
            $this->irhpApplicationEntity,
            $isSnapshot
        );

        $this->assertEquals($expectedTemplateVariables, $templateVariables);
    }

    /**
     * @dataProvider dpSnapshot
     */
    public function testGetTemplateVariablesWithoutCountries($isSnapshot)
    {
        $hasRestrictedCountries = false;

        $this->irhpApplicationEntity->shouldReceive('getAnswer')
            ->with($this->applicationStepEntity)
            ->andReturn($hasRestrictedCountries);

        $expectedTemplateVariables = [
            'hasRestrictedCountries' => $hasRestrictedCountries,
            'countryNames' => []
        ];

        $templateVariables = $this->restrictedCountriesAnswerSummaryProvider->getTemplateVariables(
            $this->applicationStepEntity,
            $this->irhpApplicationEntity,
            $isSnapshot
        );

        $this->assertEquals($expectedTemplateVariables, $templateVariables);
    }

    public function dpSnapshot()
    {
        return [
            [true],
            [false]
        ];
    }
}
