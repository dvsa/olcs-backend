<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Messaging\Subjects;

use ArrayIterator;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\QueryHandler\Messaging\Subjects\All;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Query\Messaging\Subjects\All as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use LmcRbacMvc\Service\AuthorizationService;
use Mockery as m;

class AllTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new All();
        $this->mockRepo(Repository\MessagingSubject::class, Repository\MessagingSubject::class);

        $this->mockedSmServices = [
            'SectionAccessService' => m::mock(),
            AuthorizationService::class => m::mock(AuthorizationService::class)
                ->shouldReceive('isGranted')
                ->with(Permission::SELFSERVE_USER, null)
                ->andReturn(true)
                ->shouldReceive('isGranted')
                ->with(Permission::INTERNAL_USER, null)
                ->andReturn(false)
                ->getMock(),
        ];

        $this->mockedSmServices[AuthorizationService::class]
            ->shouldReceive('getIdentity->getUser->getId')
            ->andReturn(1);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create([
            'organisation' => 1,
        ]);

        $subjects = new ArrayIterator([
            ['id' => 1, 'description' => 'Subject 1',],
            ['id' => 2, 'description' => 'Subject 2',],
        ]);

        $this->repoMap[Repository\MessagingSubject::class]->shouldReceive('fetchList')->once()->andReturn($subjects);

        $result = $this->sut->handleQuery($query);

        $this->assertArrayHasKey('result', $result);

        $this->assertCount(2, $result['result']);
    }
}
