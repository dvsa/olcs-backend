<?php

/**
 * Business Details Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Licence;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Licence\BusinessDetails;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Organisation as OrganisationRepo;
use Dvsa\Olcs\Transfer\Query\Licence\BusinessDetails as Qry;

/**
 * Business Details Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BusinessDetailsTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new BusinessDetails();
        $this->mockRepo('Organisation', OrganisationRepo::class);
        $this->mockRepo('Licence', OrganisationRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        /** @var OrganisationEntity $organisation */
        $organisation = m::mock(OrganisationEntity::class)->makePartial();
        $organisation->setId(222);
        $organisation->shouldReceive('jsonSerialize')
            ->andReturn(['id' => 222]);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setOrganisation($organisation);

        $licence->shouldReceive('getTradingNames->toArray')
            ->andReturn(['foo']);

        $licence->shouldReceive('getCompanySubsidiaries->toArray')
            ->andReturn(['bar']);

        $query = Qry::create(['id' => 111]);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($licence);

        $this->repoMap['Organisation']->shouldReceive('fetchBusinessDetailsById')
            ->with(222)
            ->andReturn($organisation);

        $expected = [
            'id' => 222,
            'tradingNames' => [
                'foo'
            ],
            'companySubsidiaries' => [
                'bar'
            ]
        ];

        $this->assertEquals($expected, $this->sut->handleQuery($query));
    }
}
