<?php

/**
 * PeopleTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Licence;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Licence\People as QueryHandler;
use Dvsa\Olcs\Api\Entity\Licence\Licence as Licence;
use Dvsa\Olcs\Api\Domain\Repository\OrganisationPerson as OrganisationPersonRepo;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Transfer\Query\Licence\People as Query;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\OlcsTest\Api\Entity\User as UserEntity;

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
        $this->mockRepo('Licence', LicenceRepo::class);
        $this->mockRepo('OrganisationPerson', OrganisationPersonRepo::class);

        /** @var UserEntity $currentUser */
        $currentUser = m::mock(UserEntity::class)->makePartial();
        $currentUser->shouldReceive('isAnonymous')->andReturn(false);

        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(AuthorizationService::class)
                ->shouldReceive('isGranted')->andReturn(false)->getMock(),
        ];

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($currentUser);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query::create(['id' => 111]);

        $organisation = new \Dvsa\Olcs\Api\Entity\Organisation\Organisation();
        $organisation->setId(923)->setType(new \Dvsa\Olcs\Api\Entity\System\RefData());
        $licence = new Licence($organisation, new \Dvsa\Olcs\Api\Entity\System\RefData());
        $licence->setId(432);

        $mockOp = m::mock()->shouldReceive('serialize')->with(['person'])->once()->andReturn(['OP'])->getMock();

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')->with($query)->andReturn($licence);
        $this->repoMap['OrganisationPerson']->shouldReceive('fetchListForOrganisation')->with(923)
            ->andReturn([$mockOp]);

        $response = $this->sut->handleQuery($query);
        $this->assertArraySubset(
            [
                'id' => 432,
                'hasInforceLicences' => false,
                'isExceptionalType' => false,
                'isSoleTrader' => false,
                'people' => [
                    ['OP']
                ],
            ],
            $response->serialize()
        );
    }
}
