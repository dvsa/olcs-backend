<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Application;

use DMS\PHPUnitExtensions\ArraySubset\Assert;
use Dvsa\Olcs\Api\Domain\QueryHandler\Application\People;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Transfer\Query\Application\People as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * @author Mat Evans <mat.evans@valtech.co.uk>
 * @covers \Dvsa\Olcs\Api\Domain\QueryHandler\Application\People

 */
class PeopleTest extends QueryHandlerTestCase
{
    /** @var  People */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new People();

        $this->mockRepo('Application', Repository\Application::class);
        $this->mockRepo('Licence', Repository\Licence::class);
        $this->mockRepo('OrganisationPerson', Repository\OrganisationPerson::class);
        $this->mockRepo('ApplicationOrganisationPerson', Repository\ApplicationOrganisationPerson::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $orgId = 923;
        $appId = 111;

        $query = Query::create(['id' => $appId]);

        $organisation = new Entity\Organisation\Organisation();
        $organisation->setId($orgId)->setType(new Entity\System\RefData());

        $licence = new \Dvsa\Olcs\Api\Entity\Licence\Licence($organisation, new Entity\System\RefData());
        $licence->setId(432);

        $application = new Entity\Application\Application(
            $licence,
            new Entity\System\RefData,
            1
        );
        $application->setId($appId);

        $mockOp = m::mock()
            ->shouldReceive('serialize')->with(['person' => ['title']])->once()->andReturn(['OP'])
            ->getMock();

        $mockAop = m::mock()
            ->shouldReceive('serialize')->with(['person', 'originalPerson'])->once()->andReturn(['AOP'])
            ->getMock();

        $this->repoMap['Application']->shouldReceive('fetchUsingId')->with($query)->andReturn($application);
        $this->repoMap['OrganisationPerson']
            ->shouldReceive('fetchListForOrganisation')->with($orgId)->andReturn([$mockOp]);
        $this->repoMap['ApplicationOrganisationPerson']
            ->shouldReceive('fetchListForApplication')->with($appId)->andReturn([$mockAop]);

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
        Assert::assertArraySubset(
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
