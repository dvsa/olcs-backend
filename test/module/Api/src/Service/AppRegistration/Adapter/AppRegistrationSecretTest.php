<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Service\AppRegistration\Adapter;

use Dvsa\Olcs\Api\Service\AppRegistration\Adapter\AppRegistrationSecret;
use Dvsa\Olcs\Api\Service\SecretsManager\SecretsManagerInterface;
use Mockery as m;

class AppRegistrationSecretTest extends m\Adapter\Phpunit\MockeryTestCase
{
    private AppRegistrationSecret $sut;

    public function setUp(): void
    {
        $mockSecretsManager = m::mock(SecretsManagerInterface::class);
        $mockSecretsManager->shouldReceive('getSecret')->with('txc_client_secret')->andReturn(['client_secret' => 'client_secret']);
        $this->sut = new AppRegistrationSecret($mockSecretsManager);
        parent::setUp();
    }

    public function testGetClientSecret(): void
    {
        $this->assertEquals('client_secret', $this->sut->getClientSecret('txc_client_secret'));
    }
}
