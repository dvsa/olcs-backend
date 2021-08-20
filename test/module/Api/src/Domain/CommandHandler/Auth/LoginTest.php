<?php
declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Auth;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Auth\Login;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Domain\Repository\User as UserRepo;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Auth\Adapter\OpenAm;
use Dvsa\Olcs\Auth\Service\AuthenticationServiceInterface;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Laminas\Authentication\Adapter\ValidatableAdapterInterface;
use Laminas\ServiceManager\ServiceManager;
use Mockery as m;
use Olcs\TestHelpers\Service\MocksServicesTrait;

/**
 * Class LoginTest
 * @see Login
 */
class LoginTest extends CommandHandlerTestCase
{
    use MocksServicesTrait;

    /** @var Login  */
    private $command;

    /**
     * @var AuthenticationServiceInterface|m\LegacyMockInterface|m\MockInterface
     */
    private $mockAuthenticationService;

    /**
     * @var ValidatableAdapterInterface|m\LegacyMockInterface|m\MockInterface
     */
    private $mockAdapter;

    /**
     * @test
     */
    public function handleCommand_IsCallable()
    {
        // Assert
        $this->assertIsCallable([$this->sut, 'handleCommand']);
    }

    /**
     * @test
     * @depends handleCommand_IsCallable
     */
    public function handleCommand_AdapterSetsUsernameAndPasswordFromCommand()
    {
        // Expectations
        $this->authenticationAdapter()->expects('setIdentity')->withArgs([$testUsername = 'testUsername']);
        $this->authenticationAdapter()->expects('setCredential')->withArgs([$testPassword = 'testPassword']);

        // Execute
        $this->sut->handleCommand(\Dvsa\Olcs\Transfer\Command\Auth\Login::create([
            'username' => $testUsername,
            'password' => $testPassword,
        ]));
    }

    /**
     * @test
     */
    public function handleCommand_AdapterSetsRealm_FromCommand_WhenAdapterIsOpenAM()
    {
        // Setup
        $openAMAdapter = $this->authenticationAdapter(OpenAm::class);
        $openAMAdapter->allows('setIdentity')->withArgs([$testUsername = 'testUsername']);
        $openAMAdapter->allows('setCredential')->withArgs([$testPassword = 'testPassword']);

        $this->sut = new Login($this->authenticationService(), $openAMAdapter);
        parent::setUp();

        $this->authenticationService()->expects('authenticate')->andReturns(
            new \Laminas\Authentication\Result(\Laminas\Authentication\Result::FAILURE, false)
        );

        // Expectations
        $openAMAdapter->expects('setRealm')->withArgs([$testRealm ='testRealm']);

        // Execute
        $this->sut->handleCommand(\Dvsa\Olcs\Transfer\Command\Auth\Login::create([
            'username' => $testUsername,
            'password' => $testPassword,
            'realm' => $testRealm
        ]));
    }

    /**
     * @test
     * @depends handleCommand_AdapterSetsUsernameAndPasswordFromCommand
     */
    public function handleCommand_UpdatesUserLastLoginAtWhenAuthenticationIsSuccessful()
    {
        // Expectations
        $user = m::mock(User::class);
        $this->userRepository()
            ->expects('fetchByLoginId')
            ->withArgs(['testUsername'])
            ->andReturns([$user]);
        $user->expects('setLastLoginAt');
        $this->userRepository()
            ->expects('save')
            ->withArgs([$user])
            ->andReturns(true);

        // Execute
        $result = $this->sut->handleCommand(\Dvsa\Olcs\Transfer\Command\Auth\Login::create([
            'username' => 'testUsername',
            'password' => 'testPassword',
        ]));

        // Assert
        $this->assertInstanceOf(Result::class, $result);
        $this->assertNotNull($result->getFlag('isValid'));
        $this->assertNotNull($result->getFlag('code'));
        $this->assertNotNull($result->getFlag('identity'));
        $this->assertNotNull($result->getFlag('messages'));
    }

    /**
     * @test
     * @depends handleCommand_UpdatesUserLastLoginAtWhenAuthenticationIsSuccessful
     */
    public function handleCommand_WhenAuthenticationIsSuccessfulWhenFetchByLoginIdReturnsEmptyThrowsException()
    {
        // Expectations
        $this->userRepository()
            ->expects('fetchByLoginId')
            ->withArgs(['testUsername'])
            ->andReturns([]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Updating lastLoginAt failed: loginId is not found in User table');

        // Execute
        $this->sut->handleCommand(\Dvsa\Olcs\Transfer\Command\Auth\Login::create([
            'username' => 'testUsername',
            'password' => 'testPassword',
        ]));
    }

    /**
     * @test
     * @depends handleCommand_UpdatesUserLastLoginAtWhenAuthenticationIsSuccessful
     */
    public function handleCommand_DoesNotUpdateLastLoginAtWhenAuthenticationIsNotSuccessful()
    {
        // Expectations
        $this->authenticationService()
            ->expects('authenticate')
            ->andReturns(
                new \Laminas\Authentication\Result(
                    \Laminas\Authentication\Result::FAILURE,
                    false
                )
            );

        $this->userRepository()->shouldNotHaveReceived('fetchByLoginId');

        // Execute
        $result = $this->sut->handleCommand(\Dvsa\Olcs\Transfer\Command\Auth\Login::create([
            'username' => 'testUsername',
            'password' => 'testPassword',
        ]));

        // Assert
        $this->assertInstanceOf(Result::class, $result);
        $this->assertNotNull($result->getFlag('isValid'));
        $this->assertNotNull($result->getFlag('code'));
        $this->assertEquals(\Laminas\Authentication\Result::FAILURE, $result->getFlag('code'));
        $this->assertNotNull($result->getFlag('identity'));
        $this->assertNotNull($result->getFlag('messages'));
    }

    /**
     * @test
     * @depends handleCommand_DoesNotUpdateLastLoginAtWhenAuthenticationIsNotSuccessful
     */
    public function handleCommand_ReturnsResultWithExpectedFlags()
    {
        // Execute
        $result = $this->sut->handleCommand(\Dvsa\Olcs\Transfer\Command\Auth\Login::create([
            'username' => 'testUsername',
            'password' => 'testPassword',
        ]));

        // Assert
        $this->assertInstanceOf(Result::class, $result);
        $this->assertNotNull($result->getFlag('isValid'));
        $this->assertNotNull($result->getFlag('code'));
        $this->assertNotNull($result->getFlag('identity'));
        $this->assertNotNull($result->getFlag('messages'));
    }

    public function setUp(): void
    {
        $this->setUpServiceManager();
        $this->setUpSut();

        parent::setUp();
    }

    protected function setUpSut(): void
    {
        $this->sut = new Login($this->authenticationService(), $this->authenticationAdapter());
    }

    protected function setUpDefaultServices()
    {
        $this->userRepository();
        $this->authenticationService();
        $this->authenticationAdapter();
    }

    /**
     * @return UserRepo|m\MockInterface
     */
    protected function userRepository(): m\MockInterface
    {
        if (!array_key_exists('User', $this->repoMap)) {
            $repoMock = m::mock(UserRepo::class);
            $mockUser = m::mock(User::class)->shouldIgnoreMissing();
            $repoMock->allows('fetchByLoginId')->andReturns([$mockUser])->byDefault();
            $repoMock->shouldIgnoreMissing();
            $this->repoMap['User'] = $repoMock;
        }

        return $this->repoMap['User'];
    }

    /**
     * @return AuthenticationServiceInterface|m\LegacyMockInterface|m\MockInterface
     */
    protected function authenticationService(): AuthenticationServiceInterface
    {
        if (!$this->serviceManager->has(AuthenticationServiceInterface::class)) {
            $authenticationService = m::mock(AuthenticationServiceInterface::class);
            $authenticationService->shouldIgnoreMissing();
            $authenticationService->allows('authenticate')->andReturns(new \Laminas\Authentication\Result(1, true))->byDefault();
            $this->serviceManager->setService(AuthenticationServiceInterface::class, $authenticationService);
        }

        return $this->serviceManager->get(AuthenticationServiceInterface::class);
    }

    /**
     * @return ValidatableAdapterInterface|m\LegacyMockInterface|m\MockInterface
     */
    protected function authenticationAdapter(string $adapterClass = ValidatableAdapterInterface::class): ValidatableAdapterInterface
    {
        if (!$this->serviceManager->has($adapterClass)) {
            $adapter = m::mock($adapterClass);
            $adapter->shouldIgnoreMissing();
            $adapter->allows('isValid')->andReturns(true)->byDefault();
            $adapter->allows('code')->andReturns(1)->byDefault();
            $adapter->allows('identity')->andReturns('user')->byDefault();
            $adapter->allows('getMessages')->andReturns(['Authentication was successful'])->byDefault();
            $this->serviceManager->setService($adapterClass, $adapter);
        }

        return $this->serviceManager->get($adapterClass);
    }
}
