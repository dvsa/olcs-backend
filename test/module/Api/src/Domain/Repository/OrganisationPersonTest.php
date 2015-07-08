<?php

/**
 * Organisation Person Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Person\Person;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\OrganisationPerson as OrganisationPersonRepo;

/**
 * Organisation Person Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OrganisationPersonTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(OrganisationPersonRepo::class);
    }

    public function testFetchByOrgAndPerson()
    {
        /** @var Organisation $organisation */
        $organisation = m::mock(Organisation::class)->makePartial();

        /** @var Person $person */
        $person = m::mock(Person::class)->makePartial();

        $mockQb = $this->createMockQb('{QUERY}');

        $this->mockCreateQueryBuilder($mockQb);

        $mockQuery = m::mock();

        $mockQuery->shouldReceive('execute')
            ->once()
            ->shouldReceive('getResult')
            ->once()
            ->andReturn('Foo');

        $mockQb->shouldReceive('getQuery')
            ->andReturn($mockQuery);

        $this->assertEquals('Foo', $this->sut->fetchByOrgAndPerson($organisation, $person));

        $this->assertEquals(
            '{QUERY} AND m.organisation = ' . get_class($organisation) . ' AND m.person = ' . get_class($person),
            $this->query
        );
    }
}
