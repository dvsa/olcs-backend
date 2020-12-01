<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cache;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\QueryHandler\Cache\ById as Handler;
use Dvsa\Olcs\Transfer\Query\Cache\ById as ByIdQry;
use Dvsa\Olcs\Api\Domain\Query\Cache\TranslationKey as TranslationKeyQry;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * Tests the cache handler calls the correct query (uses the translation key query as an example)
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ByIdTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = m::mock(Handler::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $mockUser = m::mock(User::class);
        $mockUser->expects('isAnonymous')->andReturnTrue();

        $authService = m::mock(AuthorizationService::class);
        $authService->expects('getIdentity->getUser')->andReturn($mockUser);

        $this->mockedSmServices = [
            CacheEncryption::class => m::mock(CacheEncryption::class),
            AuthorizationService::class => $authService,
        ];

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $cacheId = CacheEncryption::TRANSLATION_KEY_IDENTIFIER;
        $uniqueId = 'uniqueId';
        $cacheValue = 'cache value';

        $queryParams = [
            'id' => $cacheId,
            'uniqueId' => $uniqueId
        ];

        $queryHandler = m::mock(AbstractQueryHandler::class);

        $queryHandler->expects('handleQuery')
            ->with(m::type(TranslationKeyQry::class))
            ->andReturnUsing(function ($childQuery) use ($cacheId, $uniqueId, $cacheValue) {
                $this->assertEquals($uniqueId, $childQuery->getUniqueId());

                return $cacheValue;
            });

        $this->sut->expects('getQueryHandler')->withNoArgs()->andReturn($queryHandler);

        $this->mockedSmServices[CacheEncryption::class]
            ->expects('setCustomItem')
            ->with($cacheId, $cacheValue, $uniqueId);

        $query = ByIdQry::create($queryParams);
        $this->assertEquals($cacheValue, $this->sut->handleQuery($query));
    }

    public function testHandleQueryNoPermission()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(Handler::MSG_PERMISSION_ERROR);

        $queryParams = ['id' => 'some key'];

        $query = ByIdQry::create($queryParams);
        $this->sut->handleQuery($query);
    }
}
