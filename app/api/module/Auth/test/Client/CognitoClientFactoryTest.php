<?php
declare(strict_types = 1);

namespace Dvsa\Olcs\Auth\Test\Client;

use Dvsa\Authentication\Cognito\Client;
use Dvsa\Olcs\Auth\Client\CognitoClientFactory;
use Mockery as m;
use Olcs\TestHelpers\MockeryTestCase;
use Olcs\TestHelpers\Service\MocksServicesTrait;

/**
 * Class CognitoClientFactoryTest
 * @see CognitoClientFactory
 */
class CognitoClientFactoryTest extends MockeryTestCase
{
    use MocksServicesTrait;

    const CONFIG_WITH_WITH_VALID_SETTINGS = [
        CognitoClientFactory::CONFIG_CLIENT_ID => 'client_id',
        CognitoClientFactory::CONFIG_CLIENT_SECRET => 'client_secret',
        CognitoClientFactory::CONFIG_POOL_ID => 'pool_id',
        CognitoClientFactory::CONFIG_REGION => 'region',
        CognitoClientFactory::CONFIG_NBF_LEEWAY => 2,
        CognitoClientFactory::CONFIG_HTTP => [],
    ];

    /**
     * @var CognitoClientFactory
     */
    protected $sut;

    /**
     * @test
     */
    public function createService_IsCallable()
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, 'createService']);
    }

    /**
     * @test
     * @depends createService_IsCallable
     * @depends __invoke_IsCallable
     */
    public function createService_CallsInvoke()
    {
        // Setup
        $this->sut = m::mock(CognitoClientFactory::class)->makePartial();

        // Expectations
        $this->sut->expects('__invoke')->withArgs(function ($serviceManager, $requestedName) {
            $this->assertSame($this->serviceManager(), $serviceManager, 'Expected first argument to be the ServiceManager passed to createService');
            $this->assertSame(null, $requestedName, 'Expected requestedName to be NULL');
            return true;
        });

        // Execute
        $this->sut->createService($this->serviceManager());
    }

    /**
     * @test
     */
    public function __invoke_IsCallable(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, '__invoke']);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_ReturnsAnInstanceOfClient()
    {
        // Setup
        $this->setUpSut();
        $this->configService(static::CONFIG_WITH_WITH_VALID_SETTINGS);

        // Execute
        $result = $this->sut->__invoke($this->serviceManager(), null);

        // Assert
        $this->assertInstanceOf(Client::class, $result);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     */
    public function __invoke_ThrowsExceptionWhen_ConfigCognitoNamespaceNotDefined(): void
    {
        // Setup
        $this->setUpSut();
        $this->configService([]);

        // Expectations
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(CognitoClientFactory::EXCEPTION_MESSAGE_NAMESPACE_MISSING);

        // Execute
        $this->sut->__invoke($this->serviceManager(), null);
    }

    /**
     * @test
     * @depends __invoke_IsCallable
     * @dataProvider incorrectSettingsProvider
     */
    public function __invoke_ThrowsExceptionWhen_ConfigSettingsNotDefined(array $config): void
    {
        // Setup
        $this->setUpSut();
        $this->configService($config);

        // Expectations
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(CognitoClientFactory::EXCEPTION_MESSAGE_OPTION_MISSING);

        // Execute
        $this->sut->__invoke($this->serviceManager(), null);
    }

    public function setUp(): void
    {
        $this->setUpServiceManager();
    }

    protected function setUpSut(): void
    {
        $this->sut = new CognitoClientFactory();
    }

    public function incorrectSettingsProvider()
    {
        return [
            'Missing clientId' => [
                CognitoClientFactory::CONFIG_ADAPTER => [
                    CognitoClientFactory::CONFIG_CLIENT_SECRET => 'client_secret',
                    CognitoClientFactory::CONFIG_POOL_ID => 'pool_id',
                    CognitoClientFactory::CONFIG_REGION => 'region',
                ]
            ],
            'Missing clientSecret' => [
                CognitoClientFactory::CONFIG_ADAPTER => [
                    CognitoClientFactory::CONFIG_CLIENT_ID => 'client_id',
                    CognitoClientFactory::CONFIG_POOL_ID => 'pool_id',
                    CognitoClientFactory::CONFIG_REGION => 'region',
                ]
            ],
            'Missing poolId' => [
                CognitoClientFactory::CONFIG_ADAPTER => [
                    CognitoClientFactory::CONFIG_CLIENT_ID => 'client_id',
                    CognitoClientFactory::CONFIG_CLIENT_SECRET => 'client_secret',
                    CognitoClientFactory::CONFIG_REGION => 'region',
                ]
            ],
            'Missing region' => [
                CognitoClientFactory::CONFIG_ADAPTER => [
                    CognitoClientFactory::CONFIG_CLIENT_ID => 'client_id',
                    CognitoClientFactory::CONFIG_CLIENT_SECRET => 'client_secret',
                    CognitoClientFactory::CONFIG_POOL_ID => 'pool_id',
                ]
            ],
            'Missing http' => [
                CognitoClientFactory::CONFIG_ADAPTER => [
                    CognitoClientFactory::CONFIG_CLIENT_ID => 'client_id',
                    CognitoClientFactory::CONFIG_CLIENT_SECRET => 'client_secret',
                    CognitoClientFactory::CONFIG_POOL_ID => 'pool_id',
                    CognitoClientFactory::CONFIG_REGION => 'region'
                ]
            ]
        ];
    }

    /**
     * @param array|null $config
     */
    protected function configService(array $config = null)
    {
        $config = [
            CognitoClientFactory::CONFIG_NAMESPACE => [
                CognitoClientFactory::CONFIG_ADAPTERS => [
                    CognitoClientFactory::CONFIG_ADAPTER => $config
                ]
            ]
        ];

        $this->serviceManager->setService('Config', $config);
    }
}
