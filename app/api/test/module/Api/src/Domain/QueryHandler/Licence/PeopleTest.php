<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Licence;

use Dvsa\Olcs\Api\Domain\QueryHandler\Licence\People;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Query\Licence\People as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\OlcsTest\Api\Entity\User as UserEntity;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * @covers \Dvsa\Olcs\Api\Domain\QueryHandler\Licence\People
 */
class PeopleTest extends QueryHandlerTestCase
{
    /** @var People */
    protected $sut;

    public function setUp()
    {
        $this->sut = new People();
        $this->mockRepo('Licence', Repository\Licence::class);
        $this->mockRepo('OrganisationPerson', Repository\OrganisationPerson::class);

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
        $organisation
            ->setId(923)
            ->setType(new \Dvsa\Olcs\Api\Entity\System\RefData());

        $licence = new Licence($organisation, new \Dvsa\Olcs\Api\Entity\System\RefData());
        $licence->setId(432);

        $mockOp = m::mock()
            ->shouldReceive('serialize')->with(['person' => ['title']])->once()->andReturn(['OP'])
            ->getMock();

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
