<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Test\Service\OpenAm\Client;

use Dvsa\Olcs\Auth\Client\UriBuilder;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class UriBuilderTest extends MockeryTestCase
{
    public function testBuild(): void
    {
        $sut = new UriBuilder('olcs.openam');
        $this->assertEquals('olcs.openam/foo/bar', $sut->build('foo/bar'));
    }

    public function testBuildWithRealm(): void
    {
        $sut = new UriBuilder('olcs.openam', 'foo');
        $this->assertEquals('olcs.openam/foo/bar?realm=foo', $sut->build('foo/bar'));
    }

    public function testBuildWithRealmSetAfterInitialising(): void
    {
        $sut = new UriBuilder('olcs.openam');
        $sut->setRealm('foo');
        $this->assertEquals('olcs.openam/foo/bar?realm=foo', $sut->build('foo/bar'));
    }

    public function testBuildWithRealmAndQs(): void
    {
        $sut = new UriBuilder('olcs.openam', 'foo');
        $this->assertEquals('olcs.openam/foo/bar?foo=bar&realm=foo', $sut->build('foo/bar?foo=bar'));
    }
}
