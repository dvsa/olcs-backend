<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\Common;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Repository\Country as CountryRepository;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitRange as IrhpPermitRangeRepository;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Service\Permits\Common\RangeBasedRestrictedCountriesProvider;
use Dvsa\Olcs\Api\Service\Permits\Common\TypeBasedRestrictedCountriesProvider;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * RangeBasedRestrictedCountriesProviderTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class RangeBasedRestrictedCountriesProviderTest extends MockeryTestCase
{
    private $irhpPermitRangeId = 42;

    private $irhpPermitRange;

    private $irhpPermitRangeRepo;

    private $countryRepo;

    private $rangeBasedRestrictedCountriesProvider;

    private $typeBasedRestrictedCountriesProvider;

    public function setUp()
    {
        $this->irhpPermitRange = m::mock(IrhpPermitRange::class);

        $this->irhpPermitRangeRepo = m::mock(IrhpPermitRangeRepository::class);
        $this->irhpPermitRangeRepo->shouldReceive('fetchById')
            ->with($this->irhpPermitRangeId)
            ->once()
            ->andReturn($this->irhpPermitRange);

        $this->countryRepo = m::mock(CountryRepository::class);

        $this->typeBasedRestrictedCountriesProvider = m::mock(TypeBasedRestrictedCountriesProvider::class);

        $this->rangeBasedRestrictedCountriesProvider = new RangeBasedRestrictedCountriesProvider(
            $this->irhpPermitRangeRepo,
            $this->typeBasedRestrictedCountriesProvider,
            $this->countryRepo
        );

        parent::setUp();
    }

    public function testGetList()
    {
        $restrictedCountryIds = ['AT', 'GR', 'HU', 'IT', 'RU'];
        $rangeIncludedCountries = [
            m::mock(Country::class)->shouldReceive('getId')->andReturn('GR')->getMock(),
            m::mock(Country::class)->shouldReceive('getId')->andReturn('RU')->getMock(),
        ];

        $constrainedCountryIds = [0 => 'AT', 2 => 'HU', 3 => 'IT'];
        $constrainedCountries = [
            m::mock(Country::class),
            m::mock(Country::class),
            m::mock(Country::class),
        ];

        $irhpPermitTypeId = IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM;

        $this->irhpPermitRange->shouldReceive('getIrhpPermitStock->getIrhpPermitType->getId')
            ->withNoArgs()
            ->once()
            ->andReturn($irhpPermitTypeId);
        $this->irhpPermitRange->shouldReceive('getCountrys')
            ->withNoArgs()
            ->once()
            ->andReturn(new ArrayCollection($rangeIncludedCountries));

        $this->typeBasedRestrictedCountriesProvider->shouldReceive('getIds')
            ->with($irhpPermitTypeId)
            ->once()
            ->andReturn($restrictedCountryIds);

        $this->countryRepo->shouldReceive('fetchByIds')
            ->with($constrainedCountryIds, Query::HYDRATE_ARRAY)
            ->once()
            ->andReturn($constrainedCountries);

        $this->assertEquals(
            $constrainedCountries,
            $this->rangeBasedRestrictedCountriesProvider->getList($this->irhpPermitRangeId)
        );

        // 2nd call for the same range should return results already calculated
        $this->assertEquals(
            $constrainedCountries,
            $this->rangeBasedRestrictedCountriesProvider->getList($this->irhpPermitRangeId)
        );
    }
}
