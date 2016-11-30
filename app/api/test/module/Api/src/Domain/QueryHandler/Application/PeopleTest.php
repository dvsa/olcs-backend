<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Application;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Application\People as QueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\OrganisationPerson as OrganisationPersonRepo;
use Dvsa\Olcs\Api\Domain\Repository\ApplicationOrganisationPerson as ApplicationOrganisationPersonRepo;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Transfer\Query\Application\People as Query;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;

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
        $this->mockRepo('Licence', LicenceRepo::class);
        $this->mockRepo('OrganisationPerson', OrganisationPersonRepo::class);
        $this->mockRepo('ApplicationOrganisationPerson', ApplicationOrganisationPersonRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $orgId = 923;
        $appId = 111;
        $query = Query::create(['id' => $appId]);

        $organisation = new \Dvsa\Olcs\Api\Entity\Organisation\Organisation();
        $organisation->setId($orgId)->setType(new \Dvsa\Olcs\Api\Entity\System\RefData());
        $licence = new \Dvsa\Olcs\Api\Entity\Licence\Licence($organisation, new \Dvsa\Olcs\Api\Entity\System\RefData());
        $licence->setId(432);
        $application = new \Dvsa\Olcs\Api\Entity\Application\Application(
            $licence,
            new \Dvsa\Olcs\Api\Entity\System\RefData,
            1
        );
        $application->setId($appId);

        $mockOp = m::mock()->shouldReceive('serialize')->with(['person'])->once()->andReturn(['OP'])->getMock();
        $mockAop = m::mock()->shouldReceive('serialize')->with(['person', 'originalPerson'])->once()
            ->andReturn(['AOP'])->getMock();

        $this->repoMap['Application']->shouldReceive('fetchUsingId')->with($query)->andReturn($application);
        $this->repoMap['OrganisationPerson']->shouldReceive('fetchListForOrganisation')->with($orgId)
            ->andReturn([$mockOp]);
        $this->repoMap['ApplicationOrganisationPerson']->shouldReceive('fetchListForApplication')->with($appId)
            ->andReturn([$mockAop]);

        $this->repoMap['Licence']
            ->shouldReceive('fetchByOrganisationIdAndStatuses')
            ->with(
                $orgId,
                [
                    LicenceEntity::LICENCE_STATUS_VALID,
                    LicenceEntity::LICENCE_STATUS_SURRENDERED,
                    LicenceEntity::LICENCE_STATUS_CURTAILED,
                ]
            )
            ->andReturn(['foo', 'bar'])
            ->once()
            ->getMock();

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
                'hasMoreThanOneValidCurtailedOrSuspendedLicences' => true,
            ],
            $response->serialize()
        );
    }
}
