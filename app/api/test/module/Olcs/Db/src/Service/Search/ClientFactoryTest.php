<?php

namespace OlcsTest\Db\Service\Search;

use Olcs\Db\Service\Search\ClientFactory;
use Mockery as m;

/**
 * Class ClientFactoryTest
 * @package OlcsTest\Db\Service\Search
 */
class ClientFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testCreateService()
    {
        $mockSl = m::mock('Laminas\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('Config')->andReturn(
            ['elastic_search' => ['host' => 'google.com', 'port' =>4034]]
        );

        $sut = new ClientFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf('Elastica\Client', $service);
    }

    public function testCreateServiceWithException()
    {
        $mockSl = m::mock('Laminas\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('Config')->andReturn([]);

        $sut = new ClientFactory();
        $passed = false;
        try {
            $service = $sut->createService($mockSl);
        } catch (\Laminas\ServiceManager\Exception\RuntimeException $e) {
            if ($e->getMessage() === 'Elastic search config not found') {
                $passed = true;
            }
        }

        $this->assertTrue($passed, 'Expected exception not thrown or message didn\'t match');
    }
}
