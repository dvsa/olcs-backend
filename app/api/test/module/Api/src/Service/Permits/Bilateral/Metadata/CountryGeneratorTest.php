<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\Bilateral\Metadata;

use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Metadata\PeriodArrayGenerator;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Metadata\CountryGenerator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * CountryGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class CountryGeneratorTest extends MockeryTestCase
{
    private $countryId;

    private $countryName;

    private $hasCountryId;

    private $periodArray;

    private $country;

    private $irhpApplication;

    private $periodArrayGenerator;

    private $countryGenerator;

    public function setUp(): void
    {
        $this->countryId = 47;

        $this->countryName = 'Norway';

        $this->hasCountryId = 'hasCountryId';

        $this->periodArray = [
            'periodArrayKey1' => 'periodArrayValue1',
            'periodArrayKey2' => 'periodArrayValue2'
        ];

        $this->country = m::mock(Country::class);
        $this->country->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn($this->countryId);
        $this->country->shouldReceive('getCountryDesc')
            ->withNoArgs()
            ->andReturn($this->countryName);

        $this->irhpApplication = m::mock(IrhpApplication::class);
        $this->irhpApplication->shouldReceive('hasCountryId')
            ->with($this->countryId)
            ->andReturn($this->hasCountryId);

        $this->periodArrayGenerator = m::mock(PeriodArrayGenerator::class);

        $this->countryGenerator = new CountryGenerator($this->periodArrayGenerator);
    }

    public function testGenerate()
    {
        $selectedPeriodId = 42;

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock->getId')
            ->withNoArgs()
            ->andReturn($selectedPeriodId);

        $this->irhpApplication->shouldReceive('getIrhpPermitApplicationByStockCountryId')
            ->with($this->countryId)
            ->andReturn($irhpPermitApplication);

        $this->periodArrayGenerator->shouldReceive('generate')
            ->with($this->country, $irhpPermitApplication)
            ->andReturn($this->periodArray);

        $expected = [
            'id' => $this->countryId,
            'name' => $this->countryName,
            'visible' => $this->hasCountryId,
            'selectedPeriodId' => $selectedPeriodId,
            'periods' => $this->periodArray
        ];

        $this->assertEquals(
            $expected,
            $this->countryGenerator->generate($this->country, $this->irhpApplication)
        );
    }

    public function testGenerateNoPeriodSelected()
    {
        $this->irhpApplication->shouldReceive('getIrhpPermitApplicationByStockCountryId')
            ->with($this->countryId)
            ->andReturnNull();

        $this->periodArrayGenerator->shouldReceive('generate')
            ->with($this->country, null)
            ->andReturn($this->periodArray);

        $expected = [
            'id' => $this->countryId,
            'name' => $this->countryName,
            'visible' => $this->hasCountryId,
            'selectedPeriodId' => null,
            'periods' => $this->periodArray
        ];

        $this->assertEquals(
            $expected,
            $this->countryGenerator->generate($this->country, $this->irhpApplication)
        );
    }
}
