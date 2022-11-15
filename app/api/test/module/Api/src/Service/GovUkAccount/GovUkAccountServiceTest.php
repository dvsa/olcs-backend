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
}
