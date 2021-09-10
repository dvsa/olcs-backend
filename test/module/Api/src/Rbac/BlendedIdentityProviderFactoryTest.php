<?php
declare(strict_types = 1);

namespace Dvsa\OlcsTest\Api\Rbac;

use Dvsa\Authentication\Cognito\Client;
use Dvsa\Olcs\Api\Domain\Repository\User;
use Dvsa\Olcs\Api\Rbac\BlendedIdentityProvider;
use Dvsa\Olcs\Api\Rbac\BlendedIdentityProviderFactory;
use Dvsa\Olcs\Api\Rbac\JWTIdentityProvider;
use Dvsa\Olcs\Api\Rbac\JWTIdentityProviderFactory;
use Dvsa\Olcs\Api\Rbac\PidIdentityProvider;
use Laminas\Http\Request;
use Laminas\ServiceManager\ServiceManager;
use Mockery as m;
use Olcs\TestHelpers\MockeryTestCase;
use Olcs\TestHelpers\Service\MocksServicesTrait;

/**
 * Class BlendedIdentityProviderFactoryTest
 * @see BlendedIdentityProviderFactory
 */
class BlendedIdentityProviderFactoryTest extends MockeryTestCase
{
    use MocksServicesTrait;

    /**
     * @var BlendedIdentityProviderFactory
     */
    protected $sut;

    public function setUp(): void
    {
        $this->setUpServiceManager();
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
        $this->sut = m::mock(BlendedIdentityProviderFactory::class)->makePartial();

        // Expectations
        $this->sut->expects('__invoke')->withArgs(function ($serviceManager, $requestedName) {
            $this->assertSame($this->serviceManager(), $serviceManager, 'Expected first argument to be the ServiceManager passed to createService');
            $this->assertSame(BlendedIdentityProvider::class, $requestedName, 'Expected requestedName to be ' . BlendedIdentityProvider::class);
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
    public function __invoke_ReturnsAnInstanceOfBlendedIdentityProviderFactory()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $result = $this->sut->__invoke($this->serviceManager(), null);

        // Assert
        $this->assertInstanceOf(BlendedIdentityProvider::class, $result);
    }

    protected function setUpSut(): void
    {
        $this->sut = new BlendedIdentityProviderFactory();
    }

    /**
     * @param ServiceManager $serviceManager
     */
    protected function setUpDefaultServices(ServiceManager $serviceManager)
    {
        $this->serviceManager()->setService('Request', m::mock(Request::class));
        $this->serviceManager()->setService(PidIdentityProvider::class, m::mock(PidIdentityProvider::class));
        $this->serviceManager()->setService(JWTIdentityProvider::class, m::mock(JWTIdentityProvider::class));
    }
}
