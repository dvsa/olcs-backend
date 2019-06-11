<?php

/**
 * Organisation Permits Test
 *
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Organisation;

use DateTime;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\QueryHandler\Organisation\OrganisationPermits;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitWindow;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Organisation as OrganisationRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitWindow as IrhpPermitWindowRepo;
use Dvsa\Olcs\Transfer\Query\Organisation\OrganisationPermits as Qry;
use Mockery as m;

class OrganisationPermitsTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new OrganisationPermits();
        $this->mockRepo('Organisation', OrganisationRepo::class);
        $this->mockRepo('IrhpPermitWindow', IrhpPermitWindowRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 111, 'year' => 2020]);

        $irhpPermitStock = m::mock(IrhpPermitStock::class);

        $licences = [m::mock(Licence::class),m::mock(Licence::class)];

        $mockOrganisation = m::mock(Organisation::class)->makePartial();
        $mockOrganisation->shouldReceive('serialize')->andReturn(['foo' => 'bar']);
        $mockOrganisation->shouldReceive('getEligibleLicences')->with($irhpPermitStock)->once()->andReturn($licences);

        $irhpPermitWindow = m::mock(IrhpPermitWindow::class);
        $irhpPermitWindow->shouldReceive('getIrhpPermitStock')->once()->andReturn($irhpPermitStock);

        $this->repoMap['IrhpPermitWindow']
            ->shouldReceive('fetchLastOpenWindowByIrhpPermitType')
            ->with(
                IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT,
                m::type(DateTime::class),
                Query::HYDRATE_OBJECT,
                $query->getYear()
            )
            ->andReturn($irhpPermitWindow)
            ->once();

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($mockOrganisation);

        $expected = [
            'foo' => 'bar',
            'eligibleLicences' => $licences
        ];

        $this->assertEquals($expected, $this->sut->handleQuery($query)->serialize());
    }

    public function testHandleQueryWhenNoWindowIsOpen()
    {
        $query = Qry::create(['id' => 111, 'year' => 2020]);

        $mockOrganisation = m::mock(Organisation::class)->makePartial();
        $mockOrganisation->shouldReceive('serialize')->andReturn(['foo' => 'bar']);
        $mockOrganisation->shouldReceive('getEligibleLicences')->never();

        $this->repoMap['IrhpPermitWindow']
            ->shouldReceive('fetchLastOpenWindowByIrhpPermitType')
            ->with(
                IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT,
                m::type(DateTime::class),
                Query::HYDRATE_OBJECT,
                $query->getYear()
            )
            ->once()
            ->andThrow(new NotFoundException('No window available.'));

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($mockOrganisation);

        $expected = [
            'foo' => 'bar',
        ];

        $this->assertEquals($expected, $this->sut->handleQuery($query)->serialize());
    }
}
