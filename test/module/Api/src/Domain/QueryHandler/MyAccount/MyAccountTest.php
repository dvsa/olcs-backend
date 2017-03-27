<?php

/**
 * MyAccount Test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\MyAccount;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\QueryHandler\MyAccount\MyAccount;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Transfer\Query\MyAccount\MyAccount as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use ZfcRbac\Service\AuthorizationService;

/**
 * MyAccount Test
 */
class MyAccountTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new MyAccount();

        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(AuthorizationService::class)
        ];

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $userId = 1;

        /** @var User $mockUser */
        $mockUser = m::mock(User::class)->makePartial();
        $mockUser->setId($userId);
        $mockUser->shouldReceive('serialize')->andReturn(['foo']);
        $mockUser->shouldReceive('hasActivePsvLicence')->andReturn(false);
        $mockUser->shouldReceive('getNumberOfVehicles')->andReturn(2);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($mockUser);

        $query = Qry::create([]);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals(
            ['foo', 'hasActivePsvLicence' => false, 'numberOfVehicles' => 2],
            $result->serialize()
        );
    }

    public function testHandleQueryThrowsNotFoundException()
    {
        $this->setExpectedException(NotFoundException::class);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn(null);

        $query = Qry::create([]);

        $this->sut->handleQuery($query);
    }
}
