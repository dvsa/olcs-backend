<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Service\SecretsManager;

use Dvsa\Olcs\Api\Service\SecretsManager\LocalSecretsManager;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class LocalSecretsManagerTest extends MockeryTestCase
{
    private LocalSecretsManager $sut;

    public function setUp(): void
    {
        $this->sut = new LocalSecretsManager(['secret' => ["testSecret" => "testSecretValue"]]);
        parent::setUp();
    }

    public function testSecretReturned()
    {
        $this->assertEquals($this->sut->getSecret("secret"), ["testSecret" => "testSecretValue"]);
    }

    public function testSecretNotFound()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->sut->getSecret("testSecretNotFound");
    }

    public function testMultipleSecrets()
    {
        $this->sut = new LocalSecretsManager(['secret' => ['testSecret1' => 'testValue1'], 'secret2' => ['testSecret2' => 'testValue2']]);
        $actual = $this->sut->getSecrets(['secret', 'secret2']);
        $this->assertCount(2, $actual);
        $this->assertEquals('testValue1', $actual['secret']['testSecret1']);
        $this->assertEquals('testValue2', $actual['secret2']['testSecret2']);
    }
}
