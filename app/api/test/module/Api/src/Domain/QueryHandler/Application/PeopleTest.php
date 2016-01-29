<?php

/**
 * PeopleTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Application;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Application\People as QueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\OrganisationPerson as OrganisationPersonRepo;
use Dvsa\Olcs\Api\Domain\Repository\ApplicationOrganisationPerson as ApplicationOrganisationPersonRepo;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Transfer\Query\Application\People as Query;

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
        $this->mockRepo('Application', ApplicationRepo::class);
        $this->mockRepo('OrganisationPerson', OrganisationPersonRepo::class);
        $this->mockRepo('ApplicationOrganisationPerson', ApplicationOrganisationPersonRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query::create(['id' => 111]);

        $organisation = new \Dvsa\Olcs\Api\Entity\Organisation\Organisation();
        $organisation->setId(923)->setType(new \Dvsa\Olcs\Api\Entity\System\RefData());
        $licence = new \Dvsa\Olcs\Api\Entity\Licence\Licence($organisation, new \Dvsa\Olcs\Api\Entity\System\RefData());
        $licence->setId(432);
        $application = new \Dvsa\Olcs\Api\Entity\Application\Application(
            $licence,
            new \Dvsa\Olcs\Api\Entity\System\RefData,
            1
        );
        $application->setId(111);

        $mockOp = m::mock()->shouldReceive('serialize')->with(['person'])->once()->andReturn(['OP'])->getMock();
        $mockAop = m::mock()->shouldReceive('serialize')->with(['person', 'originalPerson'])->once()
            ->andReturn(['AOP'])->getMock();

        $this->repoMap['Application']->shouldReceive('fetchUsingId')->with($query)->andReturn($application);
        $this->repoMap['OrganisationPerson']->shouldReceive('fetchListForOrganisation')->with(923)
            ->andReturn([$mockOp]);
        $this->repoMap['ApplicationOrganisationPerson']->shouldReceive('fetchListForApplication')->with(111)
            ->andReturn([$mockAop]);

        $response = $this->sut->handleQuery($query);
        $this->assertArraySubset(
            [
                'id' => 111,
                'hasInforceLicences' => false,
                'isExceptionalType' => false,
                'isSoleTrader' => false,
                'people' => [
                    ['OP']
                ],
                'application-people' => [
                    ['AOP']
                ],
            ],
            $response->serialize()
        );
    }
}
