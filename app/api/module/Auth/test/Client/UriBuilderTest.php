<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Test\Client;

use Dvsa\Contracts\Auth\Exceptions\ClientException;
use Dvsa\Olcs\Auth\Client\UriBuilder;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @see UriBuilder
 */
class UriBuilderTest extends MockeryTestCase
{
    public function testBuildWithRealmNotSet(): void
    {
        $sut = new UriBuilder('internal.openam', 'selfserve.openam');

        $this->expectException(ClientException::class);
        $this->expectExceptionMessage(UriBuilder::MSG_REALM_NOT_SET);

        $sut->build('foo/bar');
    }

    public function testBuildWithIncorrectRealmSet(): void
    {
        $sut = new UriBuilder('internal.openam', 'selfserve.openam', 'incorrect');

        $this->expectException(ClientException::class);
        $this->expectExceptionMessage(UriBuilder::MSG_REALM_INCORRECT);

        $sut->build('foo/bar');
    }

    public function testBuildWithInternalRealm(): void
    {
        $sut = new UriBuilder('internal.openam', 'selfserve.openam', 'internal');
        $this->assertEquals('internal.openam/foo/bar?realm=internal', $sut->build('foo/bar'));
    }

    public function testBuildWithSelfserveRealm(): void
    {
        $sut = new UriBuilder('internal.openam', 'selfserve.openam', 'selfserve');
        $this->assertEquals('selfserve.openam/foo/bar?realm=selfserve', $sut->build('foo/bar'));
    }

    public function testBuildWithRealmAndQs(): void
    {
        $sut = new UriBuilder('internal.openam', 'selfserve.openam', 'internal');
        $this->assertEquals('internal.openam/foo/bar?foo=bar&realm=internal', $sut->build('foo/bar?foo=bar'));
    }
}
