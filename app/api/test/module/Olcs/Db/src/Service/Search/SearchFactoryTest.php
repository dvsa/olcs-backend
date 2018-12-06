<?php

namespace OlcsTest\Db\Service\Search;

use Olcs\Db\Service\Search\SearchFactory;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * Class SearchFactoryTest
 * @package OlcsTest\Db\Service\Search
 */
class SearchFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateService()
    {
        $mockClient = m::mock('Elastica\Client');
        $mockAuthService = m::mock(AuthorizationService::class);

        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('ElasticSearch\Client')->andReturn($mockClient);
        $mockSl->shouldReceive('get')->with(AuthorizationService::class)->andReturn($mockAuthService);

        $sut = new SearchFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf('Olcs\Db\Service\Search\Search', $service);
        $this->assertSame($mockClient, $service->getClient());
        $this->assertSame($mockAuthService, $service->getAuthService());
    }
}
