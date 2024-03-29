<?php

/**
 * PendingList Test
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Publication;

use Dvsa\Olcs\Api\Domain\QueryHandler\Publication\PendingList;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Publication as PublicationRepo;
use Dvsa\Olcs\Transfer\Query\Publication\PendingList as Qry;
use Mockery as m;
use LmcRbacMvc\Identity\IdentityInterface;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * PendingList Test
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PendingListTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new PendingList();
        $this->mockRepo('Publication', PublicationRepo::class);
        $mockAuthService = m::mock(AuthorizationService::class);

        $this->mockedSmServices = [
            AuthorizationService::class => $mockAuthService
        ];
        $this->sut->setAuthService($mockAuthService);
        parent::setUp();
    }

    /**
     * tests retrieving a list of pending publications (status new or generated)
     */
    public function testHandleQuery()
    {
        $count = 25;
        $query = Qry::create([]);
        $serializedResult = 'foo';

        $mockResult = m::mock();
        $mockResult->shouldReceive('serialize')->once()->andReturn($serializedResult);

        $mockUser = m::mock(User::class)->shouldReceive('getOsType')
            ->once()->andReturn('osType')->getMock();
        $mockId = m::mock(IdentityInterface::class)->shouldReceive('getUser')
            ->once()->andReturn($mockUser);
        $this->mockedSmServices[AuthorizationService::class]
            ->shouldReceive('getIdentity')
            ->atLeast()->once()
            ->andReturn(
                $mockId->getMock()
            )->getMock();

        $queryResult = [
            'results' => [0 => $mockResult],
            'count' => $count
        ];

        $this->repoMap['Publication']->shouldReceive('fetchPendingList')
            ->andReturn($queryResult);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($result['count'], $count);
        $this->assertEquals($result['result'], [$serializedResult]);
        $this->assertEquals($result['userOsType'], 'osType');
    }
}
