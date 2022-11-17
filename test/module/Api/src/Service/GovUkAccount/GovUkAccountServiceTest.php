<?php

namespace GovUkAccount;

use Dvsa\GovUkAccount\Provider\GovUkAccount;
use Dvsa\Olcs\Api\Service\GovUkAccount\GovUkAccountService;
use Dvsa\Olcs\Api\Service\GovUkAccount\Response\GetAuthorisationUrlResponse;
use PHPUnit\Framework\TestCase;
use Mockery as m;

class GovUkAccountServiceTest extends TestCase
{
    const CONFIG = [
        'redirect_uri' => [
            'logged_in' => 'logged_in_uri',
        ]
    ];

    protected GovUkAccount $provider;

    public function setUp(): void
    {
        $this->provider = m::mock(GovUkAccount::class);
    }

    public function testGetAuthorisationUrl(): void
    {
        $sut = new GovUkAccountService(self::CONFIG, $this->provider);

        $this->provider->expects('setState')->andReturn('some_state');
        $this->provider->expects('setNonce')->andReturn('some_nonce');
        $this->provider->expects('getState')->andReturn('some_state');
        $this->provider->expects('getNonce')->andReturn('some_nonce');
        $this->provider->expects('getAuthorizationUrl')->once()->with(
            m::on(function($params) {
                $this->assertArrayHasKey('scope', $params);
                $this->assertArrayHasKey('redirect_uri', $params);
                $this->assertEquals($this->provider::DEFAULT_SCOPES, $params['scope']);
                $this->assertEquals('logged_in_uri', $params['redirect_uri']);
                return true;
            })
        )->andReturn('some_url');

        $result = $sut->getAuthorisationUrl('some_state', false);

        $this->assertInstanceOf(GetAuthorisationUrlResponse::class, $result);
        $this->assertEquals('some_url', $result->getUrl());
        $this->assertEquals('some_state', $result->getState());
        $this->assertEquals('some_nonce', $result->getNonce());

    }

    public function testGetAuthorisationUrlWithIdentityAssurance(): void
    {
        $sut = new GovUkAccountService(self::CONFIG, $this->provider);

        $this->provider->expects('setState')->andReturn('some_state');
        $this->provider->expects('setNonce')->andReturn('some_nonce');
        $this->provider->expects('getState')->andReturn('some_state');
        $this->provider->expects('getNonce')->andReturn('some_nonce');
        $this->provider->expects('getAuthorizationUrl')->once()->with(
            m::on(function($params) {
                $this->assertArrayHasKey('scope', $params);
                $this->assertArrayHasKey('redirect_uri', $params);
                $this->assertArrayHasKey('vtr', $params);
                $this->assertArrayHasKey('claims', $params);
                return true;
            })
        )->andReturn('some_url');

        $result = $sut->getAuthorisationUrl('some_state', true);

        $this->assertInstanceOf(GetAuthorisationUrlResponse::class, $result);
        $this->assertEquals('some_url', $result->getUrl());
        $this->assertEquals('some_state', $result->getState());
        $this->assertEquals('some_nonce', $result->getNonce());

    }

    /**
     * @dataProvider dataProviderMeetsVectorOfTrust
     */
    public function testMeetsVectorOfTrust($actual, $minimumConfidence, $shouldPass): void
    {
        $result = GovUkAccountService::meetsVectorOfTrust($actual, $minimumConfidence);

        $this->assertEquals($shouldPass, $result);
    }

    public function dataProviderMeetsVectorOfTrust(): array
    {
        return [
            'P0 meets P0' => ['P0', 'P0', true],
            'P1 meets P0' => ['P1', 'P0', true],
            'P2 meets P0' => ['P2', 'P0', true],
            'P0 does not meet P1' => ['P0', 'P1', false],
            'P1 meets P1' => ['P1', 'P1', true],
            'P2 meets P1' => ['P2', 'P1', true],
            'P0 does not meet P2' => ['P0', 'P2', false],
            'P1 does not meet P2' => ['P1', 'P2', false],
            'P2 meets P2' => ['P2', 'P2', true],
        ];
    }

    /**
     * @depends testMeetsVectorOfTrust
     */
    public function testMeetsVectorOfTrustIsNotCaseSensitive(): void
    {
        $this->assertTrue(GovUkAccountService::meetsVectorOfTrust('p1', 'P1'));
        $this->assertTrue(GovUkAccountService::meetsVectorOfTrust('P1', 'p1'));
    }

    public function testMeetsVectorOfTrustUnsupportedMinimumConfidenceThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        GovUkAccountService::meetsVectorOfTrust('p1', 'P9000');
    }

    public function testMeetsVectorOfTrustUnsupportedActualReturnsFalse(): void
    {
        $this->assertFalse(GovUkAccountService::meetsVectorOfTrust('P9000', 'P0'));
    }
}
