<?php
declare(strict_types = 1);

namespace Dvsa\Olcs\Auth\Test\Adapter;

use Dvsa\Olcs\Api\Domain\CommandHandler\Auth\LoginFactory;
use Dvsa\Olcs\Auth\Adapter\ValidatableAdapterFactory;
use InvalidArgumentException;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Laminas\ServiceManager\ServiceManager;
use Olcs\TestHelpers\MockeryTestCase;
use Olcs\TestHelpers\Service\MocksServicesTrait;
use Mockery as m;
use stdClass;

/**
 * @see \Dvsa\Olcs\Auth\Adapter\ValidatableAdapterFactory
 */
class ValidatableAdapterFactoryTest extends MockeryTestCase
{
    use MocksServicesTrait;

    const CONFIG_WITHOUT_NAMESPACE = [];
    const CONFIG_WITHOUT_DEFAULT_ADAPTER_DEFINED = [
        LoginFactory::CONFIG_NAMESPACE => [],
    ];
    const CONFIG_WITH_ADAPTER_NOT_DEFINED = [
        LoginFactory::CONFIG_NAMESPACE => [
            LoginFactory::AUTH_CONFIG_DEFAULT_ADAPTER => 'some_adapter',
            LoginFactory::AUTH_CONFIG_ADAPTERS => []
        ]
    ];
    const CONFIG_WITH_ADAPTER_DEFINED_AND_ADAPTER_CONFIG_ADAPTER_NOT_DEFINED = [
        LoginFactory::CONFIG_NAMESPACE => [
            LoginFactory::AUTH_CONFIG_DEFAULT_ADAPTER => 'some_adapter',
            LoginFactory::AUTH_CONFIG_ADAPTERS => [
                'some_adapter' => []
            ]
        ]
    ];
    const CONFIG_WITH_ADAPTER_DEFINED = [
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
    public function __invoke_IsCallable(): void
    {
        // Setup
        $this->setUpSut();

        // Assert
        $this->assertIsCallable([$this->sut, '__invoke']);
    }

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
        $this->sut = m::mock(ValidatableAdapterFactory::class)->makePartial();

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
     * @depends __invoke_IsCallable
     */
    public function __invoke_ReturnsAnInstanceOfValidatableAdapterInterfaceWhenAdapterDefinedAsInstancedClass()
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
     * @depends __invoke_IsCallable
     */
    public function __invoke_ReturnsAnInstanceOfValidatableAdapterInterfaceWhenAdapterDefinedAsClassReferenceStringAndExistsInServiceManager()
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
     * @depends __invoke_IsCallable
     */
    public function __invoke_ReturnsAnInstanceOfValidatableAdapterInterfaceDefinedAsClassReferenceStringAndDoesNotExistInServiceManagerAndInstantiates()
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
     * @depends __invoke_IsCallable
     */
    public function __invoke_ThrowsExceptionWhen_ConfigAuthNamespaceNotDefined(): void
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
     * @depends __invoke_ThrowsExceptionWhen_ConfigAuthNamespaceNotDefined
     */
    public function __invoke_ThrowsExceptionWhen_ConfigAuthDefaultAdapterNotDefined(): void
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
     * @depends __invoke_ThrowsExceptionWhen_ConfigAuthDefaultAdapterNotDefined
     */
    public function __invoke_ThrowsExceptionWhen_ConfigAuthAdapterNotDefined(): void
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
     * @depends __invoke_ThrowsExceptionWhen_ConfigAuthAdapterNotDefined
     */
    public function __invoke_ThrowsExceptionWhen_ConfigAuthAdapterDefinedAndAdapterConfigAdapterNotDefined(): void
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
     * @depends __invoke_ThrowsExceptionWhen_ConfigAuthAdapterDefinedAndAdapterConfigAdapterNotDefined
     */
    public function __invoke_ThrowsExceptionWhen_ConfigAuthAdapterNotInstanceOfValidatableAdapterInterface(): void
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
