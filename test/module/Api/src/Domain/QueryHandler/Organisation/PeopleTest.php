<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Organisation;

use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\QueryHandler\Organisation\People as QueryHandler;
use Dvsa\Olcs\Transfer\Query\Organisation\People as Qry;
use Mockery as m;

/**
 * PeopleTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class PeopleTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('Organisation', \Dvsa\Olcs\Api\Domain\Repository\Organisation::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 724]);

        $mockOrganisation = m::mock('Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface');
        $mockOrganisation->shouldReceive('isSoleTrader')->with()->once()->andReturn('IS_SOLE_TRADER');
        $mockOrganisation->shouldReceive('getDisqualifications')->with()->once()->andReturn(
            new \Doctrine\Common\Collections\ArrayCollection(['ONE'])
        );
        $mockOrganisation->shouldReceive('serialize')->with(
            [
                'disqualifications',
                'organisationPersons' => [
                    'person' => [
                        'title',
                        'disqualifications'
                    ]
                ]
            ]
        )->once()->andReturn(['SERIALIZED']);
        $mockOrganisation->shouldReceive('isUnlicensed')->andReturn(false);

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')->with($query)->once()
            ->andReturn($mockOrganisation);

        $result = $this->sut->handleQuery($query);

        $expected = [
            'SERIALIZED',
            'isSoleTrader' => 'IS_SOLE_TRADER',
            'isDisqualified' => true,
            'licence' => null
        ];

        $this->assertSame($expected, $result->serialize());
    }

    public function testHandleQueryUnlicensed()
    {
        $query = Qry::create(['id' => 724]);

        $licence = m::mock(Licence::class);
        $licence->shouldReceive('serialize')
            ->andReturn(['licence' => 'foo']);

        $mockOrganisation = m::mock('Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface');
        $mockOrganisation->shouldReceive('isSoleTrader')->with()->once()->andReturn('IS_SOLE_TRADER');
        $mockOrganisation->shouldReceive('getDisqualifications')->with()->once()->andReturn(
            new \Doctrine\Common\Collections\ArrayCollection(['ONE'])
        );
        $mockOrganisation->shouldReceive('serialize')->with(
            [
                'disqualifications',
                'organisationPersons' => [
                    'person' => [
                        'title',
                        'disqualifications'
                    ]
                ]
            ]
        )->once()->andReturn(['SERIALIZED']);
        $mockOrganisation->shouldReceive('isUnlicensed')->andReturn(true);
        $mockOrganisation->shouldReceive('getLicences->first')
            ->andReturn($licence);

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')->with($query)->once()
            ->andReturn($mockOrganisation);

        $result = $this->sut->handleQuery($query);

        $expected = [
            'SERIALIZED',
            'isSoleTrader' => 'IS_SOLE_TRADER',
            'isDisqualified' => true,
            'licence' => ['licence' => 'foo']
        ];

        $this->assertSame($expected, $result->serialize());
    }
}
