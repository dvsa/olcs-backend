<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\User;

use Dvsa\Olcs\Api\Domain\Query\User\UserListByTrafficArea;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Api\Domain\QueryHandler\User\UserList as QueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\User as Repo;
use Dvsa\Olcs\Transfer\Query\User\UserList as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * @see QueryHandler
 */
class UserListTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('User', Repo::class);

        parent::setUp();
    }

    /**
     * @dataProvider handleQueryProvider
     */
    public function testHandleQuery($isInternal, $canAccessAll): void
    {
        $userData = [
            'isInternal' => $isInternal,
            'dataAccess' => [
                'canAccessAll' => $canAccessAll,
            ]
        ];

        $this->expectedUserDataCacheCall($userData);

        $query = Query::create(['QUERY']);

        $user = m::mock(\Dvsa\Olcs\Api\Entity\User\User::class)->makePartial();
        $user->setId(74);

        $this->repoMap['User']->shouldReceive('fetchList')->with($query, \Doctrine\ORM\Query::HYDRATE_OBJECT)
            ->andReturn([$user]);
        $this->repoMap['User']->shouldReceive('fetchCount')->with($query)->andReturn('COUNT');

        $result = $this->sut->handleQuery($query);

        $this->assertSame(74, $result['result'][0]['id']);
        $this->assertSame('COUNT', $result['count']);
    }

    public function handleQueryProvider(): array
    {
        return [
            'not internal and does not have full access' => [false, false],
            'is internal and does have full access' => [true, true],
        ];
    }

    public function testRedirectWhenInternalAndNotFullAccess(): void
    {
        $trafficAreas = ['B','C'];

        $initialQueryData = [
            'sort' => 'sort-field',
            'order' => 'ASC',
            'team' => 999,
            'organisation' => 888,
            'isInternal' => true
        ];

        $additionalData = [
            'trafficAreas' => $trafficAreas,
        ];

        $newQueryData = array_merge($initialQueryData, $additionalData);

        $query = Query::create($initialQueryData);

        $userData = [
            'isInternal' => true,
            'dataAccess' => [
                'canAccessAll' => false,
                'trafficAreas' => $trafficAreas
            ]
        ];

        $queryResult = new Result();
        $this->expectedUserDataCacheCall($userData);
        $this->expectedQuery(UserListByTrafficArea::class, $newQueryData, $queryResult);
        $this->assertSame($queryResult, $this->sut->handleQuery($query));
    }
}
