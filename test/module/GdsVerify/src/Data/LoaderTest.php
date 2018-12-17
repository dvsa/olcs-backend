<?php

namespace Dvsa\OlcsTest\GdsVerify\Data;

use Dvsa\Olcs\GdsVerify\Data\Loader;
use Mockery as m;

/**
 * Loader test
 */
class LoaderTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructorNoCache()
    {
        $sut = new Loader();
        $this->assertNull($sut->getCacheAdapter());
    }

    public function testConstructorWithCache()
    {
        $cache = m::mock(\Zend\Cache\Storage\StorageInterface::class);
        $sut = new Loader($cache);
        $this->assertSame($cache, $sut->getCacheAdapter());
    }

    public function testLoadMatchingServiceAdapterFileNoCache()
    {
        $sut = new Loader();
        $doc = $sut->loadMatchingServiceAdapterMetadata(__DIR__ .'/Metadata/msa-meta.xml');
        $this->assertInstanceOf(\Dvsa\Olcs\GdsVerify\Data\Metadata\MatchingServiceAdapter::class, $doc);
    }

    public function testLoadFederationFileNoCache()
    {
        $sut = new Loader();

        $doc = $sut->loadFederationMetadata(__DIR__ .'/Metadata/federation.xml');
        $this->assertInstanceOf(\Dvsa\Olcs\GdsVerify\Data\Metadata\Federation::class, $doc);
        $this->assertSame(
            'https://compliance-tool-reference.ida.digital.cabinet-office.gov.uk:443/SAML2/SSO',
            $doc->getSsoUrl()
        );
    }

    public function testLoadFederationUrlNoCache()
    {
        $mockResponse = m::mock(\Zend\Http\Response::class);
        $mockResponse->shouldReceive('isOk')->with()->once()->andReturn(true);
        $mockResponse->shouldReceive('getBody')->with()->once()->andReturn(
            file_get_contents(__DIR__ .'/Metadata/federation.xml')
        );

        $mockHttpClient = m::mock(\Zend\Http\Client::class);
        $mockHttpClient->shouldReceive('reset')->with()->once();
        $mockHttpClient->shouldReceive('setUri')->with('http:/foo.bar')->once();
        $mockHttpClient->shouldReceive('send')->with()->once()->andReturn($mockResponse);

        $sut = new Loader();
        $sut->setHttpClient($mockHttpClient);

        $this->assertInstanceOf(
            \Dvsa\Olcs\GdsVerify\Data\Metadata\Federation::class,
            $sut->loadFederationMetadata('http:/foo.bar')
        );
    }

    public function testLoadFederationUrlNoCacheError()
    {
        $mockResponse = m::mock(\Zend\Http\Response::class);
        $mockResponse->shouldReceive('isOk')->with()->once()->andReturn(false);

        $mockHttpClient = m::mock(\Zend\Http\Client::class);
        $mockHttpClient->shouldReceive('reset')->with()->once();
        $mockHttpClient->shouldReceive('setUri')->with('http:/foo.bar')->once();
        $mockHttpClient->shouldReceive('send')->with()->once()->andReturn($mockResponse);

        $sut = new Loader();
        $sut->setHttpClient($mockHttpClient);

        $this->expectException(
            \Dvsa\Olcs\GdsVerify\Exception::class,
            'Error getting metadata document http:/foo.bar'
        );
        $sut->loadFederationMetadata('http:/foo.bar');
    }

    public function testLoadFederationUrlCacheSet()
    {
        $xml = file_get_contents(__DIR__ .'/Metadata/federation.xml');

        $mockCache = m::mock(\Zend\Cache\Storage\StorageInterface::class);
        $mockCache->shouldReceive('hasItem')->with(md5('http:/foo.bar'))->once()->andReturn(false);
        $mockCache->shouldReceive('setItem')->with(md5('http:/foo.bar'), $xml)->once()->andReturn(false);

        $mockResponse = m::mock(\Zend\Http\Response::class);
        $mockResponse->shouldReceive('isOk')->with()->once()->andReturn(true);
        $mockResponse->shouldReceive('getBody')->with()->once()->andReturn($xml);

        $mockHttpClient = m::mock(\Zend\Http\Client::class);
        $mockHttpClient->shouldReceive('reset')->with()->once();
        $mockHttpClient->shouldReceive('setUri')->with('http:/foo.bar')->once();
        $mockHttpClient->shouldReceive('send')->with()->once()->andReturn($mockResponse);

        $sut = new Loader($mockCache);
        $sut->setHttpClient($mockHttpClient);

        $this->assertInstanceOf(
            \Dvsa\Olcs\GdsVerify\Data\Metadata\Federation::class,
            $sut->loadFederationMetadata('http:/foo.bar')
        );
    }

    public function testLoadFederationUrlCached()
    {
        $xml = file_get_contents(__DIR__ .'/Metadata/federation.xml');

        $mockCache = m::mock(\Zend\Cache\Storage\StorageInterface::class);
        $mockCache->shouldReceive('hasItem')->with(md5('http:/foo.bar'))->once()->andReturn(true);
        $mockCache->shouldReceive('getItem')->with(md5('http:/foo.bar'))->once()->andReturn($xml);

        $sut = new Loader($mockCache);

        $this->assertInstanceOf(
            \Dvsa\Olcs\GdsVerify\Data\Metadata\Federation::class,
            $sut->loadFederationMetadata('http:/foo.bar')
        );
    }

    public function testGetHttpClient()
    {
        $sut = new Loader();
        $this->assertInstanceOf(\Zend\Http\Client::class, $sut->getHttpClient());
    }
}
