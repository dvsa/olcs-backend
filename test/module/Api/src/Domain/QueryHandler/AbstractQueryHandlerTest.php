<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Api\Entity\User\Permission as PermissionEntity;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Transfer\Command\Audit as AuditCommand;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\AbstractQueryHandlerStub;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Exception;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * AbstractQueryHandlerTest
 */
class AbstractQueryHandlerTest extends QueryHandlerTestCase
{
    /** @var UserEntity */
    protected $currentUser;

    /** @var AuthorizationService */
    protected $authorizationService;

    public function setUp()
    {
        $this->sut = new AbstractQueryHandlerStub();

        $this->currentUser = m::mock(UserEntity::class)->makePartial();

        $this->authorizationService = m::mock(AuthorizationService::class);
        $this->authorizationService->shouldReceive('getIdentity->getUser')->withNoArgs()->andReturn($this->currentUser);

        $this->mockedSmServices = [
            AuthorizationService::class => $this->authorizationService,
        ];

        parent::setUp();
    }

    public function testAuditReadForAnonymousUser()
    {
        $this->currentUser->shouldReceive('isAnonymous')->andReturn(true);
        $this->authorizationService->shouldReceive('isGranted')->never();

        $entity = m::mock(Entity\Permits\IrhpApplication::class);

        static::assertNull($this->sut->auditRead($entity));
    }

    public function testAuditReadForNonInternalUser()
    {
        $this->currentUser->shouldReceive('isAnonymous')->andReturn(false);
        $this->authorizationService->shouldReceive('isGranted')->once()->with(PermissionEntity::INTERNAL_USER, null)->andReturn(false);

        $entity = m::mock(Entity\Permits\IrhpApplication::class);

        static::assertNull($this->sut->auditRead($entity));
    }

    /**
     * @dataProvider dpTestAuditRead
     */
    public function testAuditRead($entityClass, $expected)
    {
        $id = 1;

        $this->currentUser->shouldReceive('isAnonymous')->andReturn(false);
        $this->authorizationService->shouldReceive('isGranted')->with(PermissionEntity::INTERNAL_USER, null)->andReturn(true);

        $entity = m::mock($entityClass);
        $entity
            ->shouldReceive('getId')
            ->once()
            ->withNoArgs()
            ->andReturn($id);

        $this->expectedSideEffect(
            $expected,
            ['id' => $id],
            new Result()
        );

        $this->sut->auditRead($entity);
    }

    public function dpTestAuditRead()
    {
        return [
            [Entity\Organisation\Organisation::class, AuditCommand\ReadOrganisation::class],
            [Entity\Licence\Licence::class, AuditCommand\ReadLicence::class],
            [Entity\Cases\Cases::class, AuditCommand\ReadCase::class],
            [Entity\Application\Application::class, AuditCommand\ReadApplication::class],
            [Entity\Bus\BusReg::class, AuditCommand\ReadBusReg::class],
            [Entity\Tm\TransportManager::class, AuditCommand\ReadTransportManager::class],
            [Entity\Permits\IrhpApplication::class, AuditCommand\ReadIrhpApplication::class],
        ];
    }

    public function testAuditReadForUndefinedEntity()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cannot create audit read for entity, no DTO is defined');

        $id = 1;

        $this->currentUser->shouldReceive('isAnonymous')->andReturn(false);
        $this->authorizationService->shouldReceive('isGranted')->with(PermissionEntity::INTERNAL_USER, null)->andReturn(true);

        $entity = m::mock(PermissionEntity::class);
        $entity
            ->shouldReceive('getId')
            ->once()
            ->withNoArgs()
            ->andReturn($id);

        $this->sut->auditRead($entity);
    }
}
