<?php
declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Test\Service;

use Dvsa\Olcs\Auth\Service\PasswordService;
use Dvsa\Olcs\Auth\Service\PasswordServiceFactory;
use Olcs\TestHelpers\MockeryTestCase;
use Olcs\TestHelpers\Service\MocksServicesTrait;
use Mockery as m;

class PasswordServiceFactoryTest extends MockeryTestCase
{
    use MocksServicesTrait;

    /**
     * @var PasswordServiceFactory
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
        $this->sut = m::mock(PasswordServiceFactory::class)->makePartial();
        $this->setUpServiceManager();

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
    public function __invoke_ReturnsAnInstanceOfPasswordService()
    {
        // Setup
        $this->setUpSut();
        $this->setUpServiceManager();

        // Execute
        $result = $this->sut->__invoke($this->serviceManager(), null);

        // Assert
        $this->assertInstanceOf(PasswordService::class, $result);
    }

    protected function setUpSut(): void
    {
        $this->sut = new PasswordServiceFactory();
    }
}
