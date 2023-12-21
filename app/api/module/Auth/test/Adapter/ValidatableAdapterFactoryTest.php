<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Test\Adapter;

use Dvsa\Olcs\Api\Domain\CommandHandler\Auth\LoginFactory;
use Dvsa\Olcs\Auth\Adapter\ValidatableAdapterFactory;
use InvalidArgumentException;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Laminas\ServiceManager\ServiceManager;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\OlcsTest\MocksServicesTrait;
use Mockery as m;
use stdClass;

/**
 * @see \Dvsa\Olcs\Auth\Adapter\ValidatableAdapterFactory
 */
class ValidatableAdapterFactoryTest extends MockeryTestCase
{
    use MocksServicesTrait;

    public const CONFIG_WITHOUT_NAMESPACE = [];
    public const CONFIG_WITHOUT_DEFAULT_ADAPTER_DEFINED = [
        LoginFactory::CONFIG_NAMESPACE => [],
    ];
    public const CONFIG_WITH_ADAPTER_NOT_DEFINED = [
        LoginFactory::CONFIG_NAMESPACE => [
            LoginFactory::AUTH_CONFIG_DEFAULT_ADAPTER => 'some_adapter',
            LoginFactory::AUTH_CONFIG_ADAPTERS => []
        ]
    ];
    public const CONFIG_WITH_ADAPTER_DEFINED_AND_ADAPTER_CONFIG_ADAPTER_NOT_DEFINED = [
        LoginFactory::CONFIG_NAMESPACE => [
            LoginFactory::AUTH_CONFIG_DEFAULT_ADAPTER => 'some_adapter',
            LoginFactory::AUTH_CONFIG_ADAPTERS => [
                'some_adapter' => []
            ]
        ]
    ];
    public const CONFIG_WITH_ADAPTER_DEFINED = [
        LoginFactory::CONFIG_NAMESPACE => [
            LoginFactory::AUTH_CONFIG_DEFAULT_ADAPTER => 'some_adapter',
            LoginFactory::AUTH_CONFIG_ADAPTERS => [
                'some_adapter' => [
                    LoginFactory::ADAPTER_CONFIG_ADAPTER => ValidatableAdapterInterface::class,
                ]
            ]
        ]
    ];

    /**
     * @var ValidatableAdapterFactory
     */
    protected $sut;

    /**
     * @test
     */
    public function invokeIsCallable(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, '__invoke']);
    }

    /**
     * @test
     * @depends invokeIsCallable
     */
    public function invokeReturnsAnInstanceOfValidatableAdapterInterfaceWhenAdapterDefinedAsInstancedClass()
    {
        // Setup
        $this->setUpSut();
        $this->configureAdapter($expectedAdapter = $this->getValidatableAdapterMock());

        // Execute
        $result = $this->sut->__invoke($this->serviceManager(), null);

        // Assert
        $this->assertSame($expectedAdapter, $result, 'Provided adapter does not match the expectedAdapter instance configured');
    }

    /**
     * @test
     * @depends invokeIsCallable
     */
    public function invokeReturnsAnInstanceOfValidatableAdapterInterfaceWhenAdapterDefinedAsClassReferenceStringAndExistsInServiceManager()
    {
        // Setup
        $this->setUpSut();
        $this->configureAdapter('foo');
        $this->serviceManager()->setService('foo', $expectedAdapter = $this->getValidatableAdapterMock());

        // Execute
        $result = $this->sut->__invoke($this->serviceManager(), null);

        // Assert
        $this->assertSame($expectedAdapter, $result);
    }

    /**
     * @test
     * @depends invokeIsCallable
     */
    public function invokeReturnsAnInstanceOfValidatableAdapterInterfaceDefinedAsClassReferenceStringAndDoesNotExistInServiceManagerAndInstantiates()
    {
        // Setup
        $this->setUpSut();
        $this->configureAdapter(MockAdapter::class);

        // Execute
        $result = $this->sut->__invoke($this->serviceManager(), null);

        // Assert
        $this->assertInstanceOf(MockAdapter::class, $result);
    }

    /**
     * @test
     * @depends invokeIsCallable
     */
    public function invokeThrowsExceptionWhenConfigAuthNamespaceNotDefined(): void
    {
        // Setup
        $this->setUpSut();
        $this->configService(static::CONFIG_WITHOUT_NAMESPACE);

        // Expectations
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Config namespace is not defined: ' . LoginFactory::CONFIG_NAMESPACE);

        // Execute
        $this->sut->__invoke($this->serviceManager(), null);
    }

    /**
     * @test
     * @depends invokeThrowsExceptionWhenConfigAuthNamespaceNotDefined
     */
    public function invokeThrowsExceptionWhenConfigAuthDefaultAdapterNotDefined(): void
    {
        // Setup
        $this->setUpSut();
        $this->configService(static::CONFIG_WITHOUT_DEFAULT_ADAPTER_DEFINED);

        // Expectations
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Default adapter not defined: ' . ValidatableAdapterFactory::AUTH_CONFIG_DEFAULT_ADAPTER);

        // Execute
        $this->sut->__invoke($this->serviceManager(), null);
    }

    /**
     * @test
     * @depends invokeThrowsExceptionWhenConfigAuthDefaultAdapterNotDefined
     */
    public function invokeThrowsExceptionWhenConfigAuthAdapterNotDefined(): void
    {
        // Setup
        $this->setUpSut();
        $this->configService(static::CONFIG_WITH_ADAPTER_NOT_DEFINED);

        // Expectations
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing config for default adapter: some_adapter');

        // Execute
        $this->sut->__invoke($this->serviceManager(), null);
    }

    /**
     * @test
     * @depends invokeThrowsExceptionWhenConfigAuthAdapterNotDefined
     */
    public function invokeThrowsExceptionWhenConfigAuthAdapterDefinedAndAdapterConfigAdapterNotDefined(): void
    {
        // Setup
        $this->setUpSut();
        $this->configService(static::CONFIG_WITH_ADAPTER_DEFINED_AND_ADAPTER_CONFIG_ADAPTER_NOT_DEFINED);

        // Expectations
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Adaptor class is not defined in the adapter configuration for adaptor: some_adapter');

        // Execute
        $this->sut->__invoke($this->serviceManager(), null);
    }

    /**
     * @test
     * @depends invokeThrowsExceptionWhenConfigAuthAdapterDefinedAndAdapterConfigAdapterNotDefined
     */
    public function invokeThrowsExceptionWhenConfigAuthAdapterNotInstanceOfValidatableAdapterInterface(): void
    {
        // Setup
        $this->setUpSut();
        $this->configureAdapter(new stdClass());

        // Expectations
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Defined adapter is not instance of ' . \Laminas\Authentication\Adapter\ValidatableAdapterInterface::class);

        // Execute
        $this->sut->__invoke($this->serviceManager(), null);
    }

    public function setUp(): void
    {
        $this->setUpServiceManager();
    }

    protected function setUpSut(): void
    {
        $this->sut = new ValidatableAdapterFactory();
    }

    /**
     * @param ServiceManager $serviceManager
     */
    protected function setUpDefaultServices(ServiceManager $serviceManager)
    {
        $this->configService();
    }

    /**
     * @param array|null $config
     * @return array
     */
    protected function configService(array $config = null): array
    {
        if (! $this->serviceManager->has('config')) {
            $this->serviceManager->setService('config', static::CONFIG_WITH_ADAPTER_DEFINED);
        }

        if (! is_null($config)) {
            $this->serviceManager->setService('config', $config);
        }

        return $this->serviceManager->get('config');
    }

    /**
     * @param null $adapter
     */
    protected function configureAdapter($adapter = null): void
    {
        $config = static::CONFIG_WITH_ADAPTER_DEFINED;
        $config[LoginFactory::CONFIG_NAMESPACE][LoginFactory::AUTH_CONFIG_ADAPTERS]['some_adapter'][LoginFactory::ADAPTER_CONFIG_ADAPTER] = $adapter;
        $this->configService($config);
    }

    /**
     * @return MockAdapter
     */
    protected function getValidatableAdapterMock(): MockAdapter
    {
        return new MockAdapter();
    }
}
