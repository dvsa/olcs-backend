<?php
declare(strict_types = 1);

namespace Dvsa\OlcsTest\Api\Rbac;

use Dvsa\Authentication\Cognito\Client;
use Dvsa\Contracts\Auth\Exceptions\InvalidTokenException;
use Dvsa\Olcs\Api\Domain\Repository\User;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Api\Rbac\BlendedIdentityProvider;
use Dvsa\Olcs\Api\Rbac\Identity;
use Dvsa\Olcs\Api\Rbac\IdentityProviderInterface;
use Dvsa\Olcs\Api\Rbac\JWTIdentityProvider;
use Dvsa\Olcs\Api\Rbac\PidIdentityProvider;
use Laminas\Console\Request as ConsoleRequest;
use Laminas\Http\Headers;
use Laminas\Http\Request;
use Laminas\ServiceManager\ServiceManager;
use Mockery as m;
use Mockery\MockInterface;
use Olcs\TestHelpers\MockeryTestCase;
use Olcs\TestHelpers\Service\MocksServicesTrait;

/**
 * Class BlendedIdentityProviderTest
 * @see BlendedIdentityProvider
 */
class BlendedIdentityProviderTest extends MockeryTestCase
{
    use MocksServicesTrait;

    /**
     * @var BlendedIdentityProvider
     */
    protected $sut;

    /**
     * @test
     */
    public function getIdentity_CallsJWTIdentityProvider_ForConsoleRequest()
    {
        // Setup
        $this->sut = new BlendedIdentityProvider(new ConsoleRequest(), $this->jwtIdentityProvider(), $this->pidIdentityProvider());

        // Expects
        $jwtIP = $this->jwtIdentityProvider();
        $jwtIP->expects('getIdentity')->once();

        $pidIP = $this->pidIdentityProvider();
        $pidIP->expects('getIdentity')->never();

        // Execute
        $this->sut->getIdentity();
    }

    /**
     * @test
     */
    public function getIdentity_CallsPidIdentityProvider_ForPidHeader()
    {
        // Setup
        $this->setUpSut();

        $headers = new Headers();
        $headers->addHeaders([
             'X-PID' => 'header'
        ]);
        $this->request()->setHeaders($headers);

        // Expects
        $jwtIP = $this->jwtIdentityProvider();
        $jwtIP->expects('getIdentity')->never();

        $pidIP = $this->pidIdentityProvider();
        $pidIP->expects('getIdentity')->once();

        // Execute
        $this->sut->getIdentity();
    }

    /**
     * @test
     */
    public function getIdentity_CallsJWTIdentityProvider_ForJWTHeader()
    {
        // Setup
        $this->setUpSut();

        $headers = new Headers();
        $headers->addHeaders([
            JWTIdentityProvider::HEADER_NAME => 'header'
        ]);
        $this->request()->setHeaders($headers);

        // Expects
        $jwtIP = $this->jwtIdentityProvider();
        $jwtIP->expects('getIdentity')->once();

        $pidIP = $this->pidIdentityProvider();
        $pidIP->expects('getIdentity')->never();

        // Execute
        $this->sut->getIdentity();
    }

    /**
     * @test
     */
    public function getIdentity_ReturnsAnonUser_ForMissingHeader()
    {
        // Setup
        $this->setUpSut();

        // Execute
        $identity = $this->sut->getIdentity();

        // Assert
        $this->assertTrue($identity->getUser()->isAnonymous());
    }


    public function setUp(): void
    {
        $this->setUpServiceManager();
    }

    protected function setUpSut(): void
    {
        $this->sut = new BlendedIdentityProvider(
            $this->request(),
            $this->jwtIdentityProvider(),
            $this->pidIdentityProvider()
        );
    }

    /**
     * @param ServiceManager $serviceManager
     */
    protected function setUpDefaultServices(ServiceManager $serviceManager)
    {
        $this->request();
        $this->jwtIdentityProvider();
        $this->pidIdentityProvider();
    }

    /**
     * @return Request
     */
    protected function request()
    {
        if (!$this->serviceManager->has(Request::class)) {
            $instance = new Request();
            $this->serviceManager->setService(Request::class, $instance);
        }
        $instance = $this->serviceManager->get(Request::class);

        return $instance;
    }

    /**
     * @return MockInterface|JWTIdentityProvider
     */
    protected function jwtIdentityProvider()
    {
        if (!$this->serviceManager->has(JWTIdentityProvider::class)) {
            $instance = $this->setUpMockService(JWTIdentityProvider::class);
            $instance->allows('getHeaderName')->andReturn(JWTIdentityProvider::HEADER_NAME);
            $this->serviceManager->setService(JWTIdentityProvider::class, $instance);
        }
        $instance = $this->serviceManager->get(JWTIdentityProvider::class);

        return $instance;
    }

    /**
     * @return MockInterface|PidIdentityProvider
     */
    protected function pidIdentityProvider()
    {
        if (!$this->serviceManager->has(PidIdentityProvider::class)) {
            $instance = $this->setUpMockService(PidIdentityProvider::class);
            $instance->allows('getHeaderName')->andReturn('X-PID');
            $this->serviceManager->setService(PidIdentityProvider::class, $instance);
        }
        $instance = $this->serviceManager->get(PidIdentityProvider::class);

        return $instance;
    }

}
