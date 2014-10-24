<?php

namespace OlcsTest\Db\Service\Search;

use Olcs\Db\Service\Search\SearchFactory;
use Mockery as m;

/**
 * Class SearchFactoryTest
 * @package OlcsTest\Db\Service\Search
 */
class SearchFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateService()
    {
        $mockClient = m::mock('Elastica\Client');

        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('ElasticSearch\Client')->andReturn($mockClient);

        $sut = new SearchFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf('Olcs\Db\Service\Search\Search', $service);
        $this->assertSame($mockClient, $service->getClient());
    }
}
