<?php

namespace OlcsTest\Db\Service\Search;

use Elastica\Client;
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
        $service = $sut->__invoke($mockSl, Client::class);

        $this->assertInstanceOf(Client::class, $service);
    }

    public function testCreateServiceWithException()
    {
        $mockSl = m::mock('Laminas\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('Config')->andReturn([]);

        $sut = new ClientFactory();
        $passed = false;
        try {
            $service = $sut->__invoke($mockSl, Client::class);
        } catch (\Laminas\ServiceManager\Exception\RuntimeException $e) {
            if ($e->getMessage() === 'Elastic search config not found') {
                $passed = true;
            }
        }

        $this->assertTrue($passed, 'Expected exception not thrown or message didn\'t match');
    }
}
