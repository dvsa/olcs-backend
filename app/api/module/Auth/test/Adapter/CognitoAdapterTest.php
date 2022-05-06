<?php
declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Test\Adapter;

use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;
use Aws\Exception\AwsException;
use Dvsa\Authentication\Cognito\Client;
use Dvsa\Contracts\Auth\AbstractResourceOwner;
use Dvsa\Contracts\Auth\AccessTokenInterface;
use Dvsa\Contracts\Auth\Exceptions\ChallengeException;
use Dvsa\Contracts\Auth\Exceptions\ClientException;
use Dvsa\Contracts\Auth\Exceptions\InvalidTokenException;
use Dvsa\Contracts\Auth\ResourceOwnerInterface;
use Dvsa\Olcs\Auth\Adapter\CognitoAdapter;
use Dvsa\Olcs\Auth\Exception\ChangePasswordException;
use Dvsa\Olcs\Transfer\Result\Auth\ChangeExpiredPasswordResult;
use Dvsa\Olcs\Transfer\Result\Auth\ChangePasswordResult;
use Laminas\Authentication\Result;
use Mockery as m;
use Olcs\TestHelpers\MockeryTestCase;

/**
 * Class CognitoAdapterTest
 * @see CognitoAdapter
 */
class CognitoAdapterTest extends MockeryTestCase
{
    /**
     * @test
     */
    public function authenticate_ReturnsSuccessResult_WhenDetailsAreCorrect()
    {
        // Setup
        $mockToken = m::mock(\Dvsa\Contracts\Auth\AccessTokenInterface::class);
        $mockToken->shouldReceive('getIdToken')->andReturn('idToken');
        $mockToken->shouldReceive('getToken')->andReturn('accessToken');
        $mockToken->shouldReceive('getRefreshToken')->andReturn('refreshToken');

        $mockClient = m::mock(Client::class);
        $mockClient->shouldReceive('authenticate')->andReturn($mockToken);
        $mockClient->shouldReceive('decodeToken')->andReturn([]);
        $mockClient->shouldReceive('getResourceOwner')
            ->andReturn(m::mock(ResourceOwnerInterface::class));

        $sut = new CognitoAdapter($mockClient);
        $sut->setIdentity('identity');
        $sut->setCredential('credential');

        // Execute
        $result = $sut->authenticate();

        // Assert
        static::assertEquals(Result::SUCCESS, $result->getCode());
        static::assertArrayHasKey('Token', $result->getIdentity());
        static::assertArrayHasKey('AccessToken', $result->getIdentity());
        static::assertArrayHasKey('IdToken', $result->getIdentity());
        static::assertArrayHasKey('RefreshToken', $result->getIdentity());
    }

    /**
     * @test
     */
    public function authenticate_ReturnsFailureResult_WhenInvalidTokenExceptionIsThrown()
    {
        // Setup
        $mockClient = m::mock(Client::class);
        $mockClient->shouldReceive('authenticate')->andThrow(InvalidTokenException::class);

        $sut = new CognitoAdapter($mockClient);
        $sut->setIdentity('identity');
        $sut->setCredential('credential');

        // Execute
        $result = $sut->authenticate();

        // Assert
        static::assertEquals(Result::FAILURE, $result->getCode());
    }

    /**
     * @test
     * @dataProvider authenticateCognitoIdentityProviderExceptionDataProvider
     */
    public function authenticate_ReturnsExpectedFailureResult_WhenCognitoIdentityProviderExceptionIsThrown(
        string $awsErrorCode,
        string $awsErrorMessage,
        int $expectedResultCode
    ) {
        // Setup
        $previousException = m::mock(CognitoIdentityProviderException::class);
        $previousException->expects('getAwsErrorCode')->andReturn($awsErrorCode);


        $mockClient = m::mock(Client::class);
        $mockClient->shouldReceive('authenticate')->andThrow(new ClientException($awsErrorMessage, 0, $previousException));

        $sut = new CognitoAdapter($mockClient);
        $sut->setIdentity('identity');
        $sut->setCredential('credential');

        // Execute
        $result = $sut->authenticate();

        // Assert
        static::assertEquals($expectedResultCode, $result->getCode());
    }

    public function authenticateCognitoIdentityProviderExceptionDataProvider(): array
    {
        return [
            'User not found' => [
                CognitoAdapter::EXCEPTION_USER_NOT_FOUND,
                'message',
                Result::FAILURE_IDENTITY_NOT_FOUND
            ],
            'Incorrect credentials' => [
                CognitoAdapter::EXCEPTION_NOT_AUTHORIZED,
                CognitoAdapter::MESSAGE_INCORRECT_USERNAME_OR_PASSWORD,
                Result::FAILURE_CREDENTIAL_INVALID
            ],
            'User disabled' => [
                CognitoAdapter::EXCEPTION_NOT_AUTHORIZED,
                CognitoAdapter::MESSAGE_USER_IS_DISABLED,
                CognitoAdapter::FAILURE_ACCOUNT_DISABLED
            ]
        ];
    }

    /**
     * @test
     */
    public function authenticate_ReturnsChallengeResult_WhenChallengeExceptionIsThrown()
    {
        // Setup
        $exception = new ChallengeException();
        $exception->setChallengeName('challengeName');
        $exception->setParameters([]);
        $exception->setSession('session');

        $mockClient = m::mock(Client::class);
        $mockClient->shouldReceive('authenticate')->andThrow($exception);

        $sut = new CognitoAdapter($mockClient);
        $sut->setIdentity('identity');
        $sut->setCredential('credential');

        // Execute
        $result = $sut->authenticate();

        // Assert
        static::assertEquals(CognitoAdapter::SUCCESS_WITH_CHALLENGE, $result->getCode());
    }

    /**
     * @test
     */
    public function changedExpiredPassword_ReturnsSuccessResult_WhenDetailsAreCorrect()
    {
        // Setup
        $mockToken = m::mock(\Dvsa\Contracts\Auth\AccessTokenInterface::class);
        $mockToken->shouldReceive('getIdToken')->andReturn('idToken');
        $mockToken->shouldReceive('getToken')->andReturn('accessToken');
        $mockToken->shouldReceive('getRefreshToken')->andReturn('refreshToken');

        $mockClient = m::mock(Client::class);
        $mockClient->shouldReceive('responseToAuthChallenge')->andReturn($mockToken);
        $mockClient->shouldReceive('decodeToken')->andReturn([]);
        $mockClient->shouldReceive('getResourceOwner')
            ->andReturn(m::mock(ResourceOwnerInterface::class));

        $previousException = m::mock(AwsException::class);
        $previousException->expects('getAwsErrorCode')->andReturn(CognitoAdapter::EXCEPTION_NOT_AUTHORIZED);
        $previousException->expects('getAwsErrorMessage')->andReturn(CognitoAdapter::MESSAGE_INCORRECT_USERNAME_OR_PASSWORD);
        $mockClient->shouldReceive('authenticate')->andThrow(new ClientException('', 0, $previousException));

        $sut = new CognitoAdapter($mockClient);

        // Execute
        $result = $sut->changeExpiredPassword('newPassword', 'challengeSession', 'username');

        // Assert
        static::assertEquals(Result::SUCCESS, $result->getCode());
        static::assertArrayHasKey('Token', $result->getIdentity());
        static::assertArrayHasKey('AccessToken', $result->getIdentity());
        static::assertArrayHasKey('IdToken', $result->getIdentity());
        static::assertArrayHasKey('RefreshToken', $result->getIdentity());
    }

    /**
     * @test
     */
    public function changedExpiredPassword_ReturnsExpectedFailureResult_WhenInvalidTokenExceptionIsThrown()
    {
        // Setup
        $mockClient = m::mock(Client::class);
        $mockClient->shouldReceive('responseToAuthChallenge')
            ->andThrow(InvalidTokenException::class);

        $previousException = m::mock(AwsException::class);
        $previousException->expects('getAwsErrorCode')->andReturn(CognitoAdapter::EXCEPTION_NOT_AUTHORIZED);
        $previousException->expects('getAwsErrorMessage')->andReturn(CognitoAdapter::MESSAGE_INCORRECT_USERNAME_OR_PASSWORD);
        $mockClient->shouldReceive('authenticate')->andThrow(new ClientException('', 0, $previousException));

        $sut = new CognitoAdapter($mockClient);

        // Execute
        $result = $sut->changeExpiredPassword('newPassword', 'challengeSession', 'username');

        // Assert
        static::assertEquals(ChangeExpiredPasswordResult::FAILURE, $result->getCode());
    }

    /**
     * @test
     */
    public function changedExpiredPassword_ReturnsChallengeResult_WhenChallengeExceptionIsThrown()
    {
        // Setup
        $exception = new ChallengeException();
        $exception->setChallengeName('challengeName');
        $exception->setParameters([]);
        $exception->setSession('session');

        $mockClient = m::mock(Client::class);
        $mockClient->shouldReceive('responseToAuthChallenge')->andThrow($exception);

        $previousException = m::mock(AwsException::class);
        $previousException->expects('getAwsErrorCode')->andReturn(CognitoAdapter::EXCEPTION_NOT_AUTHORIZED);
        $previousException->expects('getAwsErrorMessage')->andReturn(CognitoAdapter::MESSAGE_INCORRECT_USERNAME_OR_PASSWORD);
        $mockClient->shouldReceive('authenticate')->andThrow(new ClientException('', 0, $previousException));

        $sut = new CognitoAdapter($mockClient);

        // Execute
        $result = $sut->changeExpiredPassword('newPassword', 'challengeSession', 'username');

        // Assert
        static::assertEquals(CognitoAdapter::SUCCESS_WITH_CHALLENGE, $result->getCode());
    }

    /**
     * @param string $awsErrorCode
     * @param int $expectedResultCode
     * @test
     * @dataProvider changedExpiredPasswordClientExceptionDataProvider
     */
    public function changedExpiredPassword_ReturnsExpectedResult_WhenClientExceptionIsThrown(string $awsErrorCode, int $expectedResultCode)
    {
        // Setup
        $previousException = m::mock(AwsException::class);
        $previousException->expects('getAwsErrorCode')->andReturn($awsErrorCode);

        $mockClient = m::mock(Client::class);
        $mockClient->shouldReceive('responseToAuthChallenge')
            ->andThrow(new ClientException('null', 0, $previousException));

        $previousException = m::mock(AwsException::class);
        $previousException->expects('getAwsErrorCode')->andReturn(CognitoAdapter::EXCEPTION_NOT_AUTHORIZED);
        $previousException->expects('getAwsErrorMessage')->andReturn(CognitoAdapter::MESSAGE_INCORRECT_USERNAME_OR_PASSWORD);
        $mockClient->shouldReceive('authenticate')->andThrow(new ClientException('', 0, $previousException));

        $sut = new CognitoAdapter($mockClient);

        // Execute
        $result = $sut->changeExpiredPassword('newPassword', 'challengeSession', 'username');

        // Assert
        static::assertEquals($expectedResultCode, $result->getCode());
    }

    public function changedExpiredPasswordClientExceptionDataProvider(): array
    {
        return [
            'Invalid Password' => [CognitoAdapter::EXCEPTION_INVALID_PASSWORD, ChangeExpiredPasswordResult::FAILURE_NEW_PASSWORD_INVALID],
            'Unauthorised' => [CognitoAdapter::EXCEPTION_NOT_AUTHORIZED, ChangeExpiredPasswordResult::FAILURE_NOT_AUTHORIZED],
            'Generic' => ['generic', ChangeExpiredPasswordResult::FAILURE]
        ];
    }

    /**
     * @test
     */
    public function changedExpiredPassword_ReturnsFailureNewPasswordIsExistingResult_WhenNewPasswordIsSameAsExisting()
    {
        // Setup
        $exception = new ChallengeException();
        $exception->setChallengeName('NEW_PASSWORD_REQUIRED');
        $exception->setParameters([]);
        $exception->setSession('session');

        $mockClient = m::mock(Client::class);
        $mockClient->shouldReceive('authenticate')->andThrow($exception);

        $sut = new CognitoAdapter($mockClient);

        // Execute
        $result = $sut->changeExpiredPassword('newPassword', 'challengeSession', 'username');

        // Assert
        static::assertInstanceOf(ChangeExpiredPasswordResult::class, $result);
        static::assertEquals(ChangeExpiredPasswordResult::FAILURE_NEW_PASSWORD_MATCHES_OLD, $result->getCode());
    }

    /**
     * @test
     */
    public function changedExpiredPassword_ReturnsGeneralFailureResult_WhenAuthenticateThrowsClientError()
    {
        // Setup
        $exception = new ClientException('', 0, m::mock(AwsException::class)->shouldIgnoreMissing());

        $mockClient = m::mock(Client::class);
        $mockClient->shouldReceive('authenticate')->andThrow($exception);

        $sut = new CognitoAdapter($mockClient);

        // Execute
        $result = $sut->changeExpiredPassword('newPassword', 'challengeSession', 'username');

        // Assert
        static::assertInstanceOf(ChangeExpiredPasswordResult::class, $result);
        static::assertEquals(ChangeExpiredPasswordResult::FAILURE_CLIENT_ERROR, $result->getCode());
    }

    /**
     * @test
     * @dataProvider changedPasswordClientExceptionDataProvider
     */
    public function changePassword_ReturnsExpectedResult_WhenClientExceptionIsThrown_OnPasswordChange(string $awsErrorCode, int $expectedResultCode): void
    {
        // Setup
        $previousException = m::mock(AwsException::class);
        $previousException->expects('getAwsErrorCode')->andReturn($awsErrorCode);

        $identifier = 'identifier';
        $previousPassword = 'previous password';
        $newPassword = 'new password';

        $mockClient = m::mock(Client::class);
        $mockClient->shouldReceive('authenticate');
        $mockClient->shouldReceive('changePassword')
            ->andThrow(new ClientException('null', 0, $previousException));

        $sut = new CognitoAdapter($mockClient);

        $result = $sut->changePassword($identifier, $previousPassword, $newPassword);

        // Assert
        static::assertEquals($expectedResultCode, $result->getCode());
    }

    public function changedPasswordClientExceptionDataProvider(): array
    {
        return [
            'Invalid Password' => [CognitoAdapter::EXCEPTION_INVALID_PASSWORD, ChangePasswordResult::FAILURE_NEW_PASSWORD_INVALID],
            'Unauthorised' => [CognitoAdapter::EXCEPTION_NOT_AUTHORIZED, ChangePasswordResult::FAILURE_NOT_AUTHORIZED],
            'Generic' => ['generic', ChangePasswordResult::FAILURE]
        ];
    }

    /**
     * @test
     * @dataProvider changedPasswordAuthClientExceptionDataProvider
     */
    public function changePassword_ReturnsExpectedResult_WhenClientExceptionIsThrown_OnAuth(string $awsErrorCode, string $awsErrorMessage, int $expectedResultCode): void
    {
        // Setup
        $previousException = m::mock(AwsException::class);
        $previousException->expects('getAwsErrorCode')->andReturn($awsErrorCode);
        $previousException->expects('getAwsErrorMessage')->andReturn($awsErrorMessage);

        $identifier = 'identifier';
        $previousPassword = 'previous password';
        $newPassword = 'new password';

        $mockClient = m::mock(Client::class);
        $mockClient->shouldReceive('authenticate')
            ->andThrow(new ClientException('null', 0, $previousException));
        $mockClient->shouldNotReceive('changePassword');

        $sut = new CognitoAdapter($mockClient);

        $result = $sut->changePassword($identifier, $previousPassword, $newPassword);

        // Assert
        static::assertEquals($expectedResultCode, $result->getCode());
    }

    /**
     * @test
     */
    public function changePassword_ReturnsExpectedResult_WhenPasswordIsReused(): void
    {
        $identifier = 'identifier';
        $previousPassword = 'previous password';
        $newPassword = 'previous password';

        $mockClient = m::mock(Client::class);
        $mockClient->expects('authenticate');

        $sut = new CognitoAdapter($mockClient);

        $result = $sut->changePassword($identifier, $previousPassword, $newPassword);

        // Assert
        static::assertEquals(ChangePasswordResult::FAILURE_PASSWORD_REUSE, $result->getCode());
    }

    public function changedPasswordAuthClientExceptionDataProvider(): array
    {
        return [
            'Invalid previous Password' => [
                CognitoAdapter::EXCEPTION_NOT_AUTHORIZED,
                CognitoAdapter::MESSAGE_INCORRECT_USERNAME_OR_PASSWORD,
                ChangePasswordResult::FAILURE_OLD_PASSWORD_INVALID
            ],
            'Client error' => [
                CognitoAdapter::EXCEPTION_NOT_AUTHORIZED,
                'Generic error',
                ChangePasswordResult::FAILURE_CLIENT_ERROR
            ],
            'Generic' => [
                'generic',
                'Generic error',
                ChangePasswordResult::FAILURE_CLIENT_ERROR
            ]
        ];
    }

    /**
     * @test
     */
    public function changePasswordReturnsSuccessResult_WhenNoExceptions(): void
    {
        $identifier = 'identifier';
        $previousPassword = 'previous password';
        $newPassword = 'new password';

        $mockClient = m::mock(Client::class);
        $mockClient->expects('authenticate');
        $mockClient->expects('changePassword')
            ->with($identifier, $newPassword, true)
            ->andReturnTrue();

        $sut = new CognitoAdapter($mockClient);

        $result = $sut->changePassword($identifier, $previousPassword, $newPassword);

        // Assert
        static::assertEquals(ChangePasswordResult::SUCCESS, $result->getCode());
    }

    /**
     * @test
     */
    public function changePasswordReturnsSuccessResult_WhenAuthThrowsChallengeException(): void
    {
        // Setup
        $identifier = 'identifier';
        $previousPassword = 'previous password';
        $newPassword = 'new password';

        $mockClient = m::mock(Client::class);
        $mockClient->shouldReceive('authenticate')
            ->andThrow(new ChallengeException('null', 0));
        $mockClient->expects('changePassword')
            ->with($identifier, $newPassword, true)
            ->andReturnTrue();

        $sut = new CognitoAdapter($mockClient);

        $result = $sut->changePassword($identifier, $previousPassword, $newPassword);

        // Assert
        static::assertEquals(ChangePasswordResult::SUCCESS, $result->getCode());
    }

    /**
     * @test
     */
    public function register_WithException()
    {
        $this->expectException(ClientException::class);

        $mockClient = m::mock(Client::class);
        $mockClient->shouldReceive('register')
            ->andThrow(ClientException::class);

        $sut = new CognitoAdapter($mockClient);
        $sut->register('identifier', 'password', 'email');
    }

    /**
     * @test
     */
    public function refreshToken_ReturnsSuccessResult_WhenRefreshSucceeds()
    {
        // Setup
        $mockToken = m::mock(\Dvsa\Contracts\Auth\AccessTokenInterface::class);
        $mockToken->shouldReceive('getIdToken')->andReturn('newIdToken');
        $mockToken->shouldReceive('getToken')->andReturn('newAccessToken');
        $mockToken->shouldReceive('getRefreshToken')->andReturn('newRefreshToken');

        $mockClient = m::mock(Client::class);
        $mockClient->shouldReceive('refreshTokens')
            ->with('refreshToken', 'username')
            ->andReturn($mockToken);
        $mockClient->shouldReceive('decodeToken')->andReturn([]);
        $mockClient->shouldReceive('getResourceOwner')
            ->andReturn(m::mock(ResourceOwnerInterface::class));

        $sut = new CognitoAdapter($mockClient);

        // Execute
        $result = $sut->refreshToken('refreshToken', 'username');
        $identity = $result->getIdentity();

        // Assert
        static::assertEquals(Result::SUCCESS, $result->getCode());
        static::assertArrayHasKey('Token', $identity);
        static::assertInstanceOf(AccessTokenInterface::class, $identity['Token']);
        static::assertArrayHasKey('AccessToken', $identity);
        static::assertEquals('newAccessToken', $identity['AccessToken']);
        static::assertArrayHasKey('IdToken', $identity);
        static::assertEquals('newIdToken', $identity['IdToken']);
        static::assertArrayHasKey('RefreshToken', $identity);
        static::assertEquals('newRefreshToken', $identity['RefreshToken']);
    }

    /**
     * @test
     */
    public function refreshToken_ReturnsChallengeResult_WhenChallengeExceptionIsThrown()
    {
        // Setup
        $exception = new ChallengeException();
        $exception->setChallengeName('challengeName');
        $exception->setParameters([]);
        $exception->setSession('session');

        $mockClient = m::mock(Client::class);
        $mockClient->shouldReceive('refreshTokens')->andThrow($exception);

        $sut = new CognitoAdapter($mockClient);

        // Execute
        $result = $sut->refreshToken('refreshToken', 'username');

        // Assert
        static::assertEquals(CognitoAdapter::SUCCESS_WITH_CHALLENGE, $result->getCode());
    }

    /**
     * @test
     * @dataProvider refreshTokenExceptionDataProvider
     */
    public function refreshToken_ReturnsFailureResult_WhenOtherExceptionsAreThrown(string $exception)
    {
        // Setup
        $mockClient = m::mock(Client::class);
        $mockClient->shouldReceive('refreshTokens')->andThrow($exception);

        $sut = new CognitoAdapter($mockClient);

        // Execute
        $result = $sut->refreshToken('refreshToken', 'username');

        // Assert
        static::assertEquals(Result::FAILURE, $result->getCode());
    }

    public function refreshTokenExceptionDataProvider()
    {
        return [
            [InvalidTokenException::class],
            [ClientException::class]
        ];
    }

    /**
     * @test
     */
    public function getUserByIdentifier_ReturnsUser()
    {
        // Setup
        $id = '1001';
        $username = 'user4574';

        $mockUser = m::mock(ResourceOwnerInterface::class);
        $mockUser->shouldReceive('getId')->andReturn($id);

        $mockClient = m::mock(Client::class);
        $mockClient->shouldReceive('getUserByIdentifier')
            ->with($username)
            ->once()
            ->andReturn($mockUser);

        $sut = new CognitoAdapter($mockClient);

        // Execute
        $result = $sut->getUserByIdentifier($username);

        // Assert
        static::assertInstanceOf(ResourceOwnerInterface::class, $result);
        static::assertEquals($id, $result->getId());
    }

    /**
     * @test
     */
    public function registerIfNotPresent_ReturnsFalse_AndDoesNotRegistersUser_WhenUserExists()
    {
        $id = '1001';
        $username = 'user4574';
        $password = 'P@s5w0rD!';
        $email = 'test@test.localdomain';

        // Setup
        $mockClient = m::mock(Client::class);
        $mockClient->shouldReceive('getUserByIdentifier')->with($username)->once()->andReturn(
            new class(['id' => $id]) extends AbstractResourceOwner implements ResourceOwnerInterface {
                public function getId(): string
                {
                    return $this->get('id');
                }
            }
        );

        $sut = new CognitoAdapter($mockClient);

        // Execute
        $result = $sut->registerIfNotPresent($username, $password, $email);

        // Assert
        static::assertFalse($result);
    }

    /**
     * @test
     */
    public function registerIfNotPresent_ReturnsTrue_AndRegistersUser_WhenUserNotExists()
    {
        $username = 'user4574';
        $password = 'P@s5w0rD!';
        $email = 'test@test.localdomain';
        $attributes = [];

        // Setup
        $previousException = m::mock(AwsException::class);
        $previousException->expects('getAwsErrorCode')->andReturn(CognitoAdapter::EXCEPTION_USER_NOT_FOUND);
        $exception = new ClientException('null', 0, $previousException);

        $mockClient = m::mock(Client::class);
        $mockClient->shouldReceive('getUserByIdentifier')->with($username)->once()->andThrow($exception);

        $compiledAttributes = array_merge(['email' => $email], $attributes);
        $mockClient->shouldReceive('register')->with($username, $password, $compiledAttributes)->once();

        $sut = new CognitoAdapter($mockClient);

        // Execute
        $result = $sut->registerIfNotPresent($username, $password, $email, $attributes);

        // Assert
        static::assertTrue($result);
    }

    /**
     * @test
     */
    public function registerIfNotPresent_BubblesUnexpectedException_WhenClientThrowsUnexpectedException()
    {
        $username = 'user4574';
        $password = 'P@s5w0rD!';
        $email = 'test@test.localdomain';
        $attributes = [];

        // Setup
        $previousException = m::mock(AwsException::class);
        $previousException->expects('getAwsErrorCode')->andReturn('SomeOtherThing');
        $exception = new ClientException('null', 0, $previousException);

        $mockClient = m::mock(Client::class);
        $mockClient->shouldReceive('getUserByIdentifier')->with($username)->once()->andThrow($exception);

        $sut = new CognitoAdapter($mockClient);

        $this->expectException(ClientException::class);

        // Execute
        $sut->registerIfNotPresent($username, $password, $email, $attributes);
    }

    /**
     * @test
     */
    public function doesUserExist_ReturnsTrue_WhenUserExistsInCognito(): void
    {
        // Setup
        $username = 'user4574';

        $mockClient = m::mock(Client::class);
        $mockClient->shouldReceive('getUserByIdentifier')->with($username)->once()->andReturn(
            new class(['id' => 1001]) extends AbstractResourceOwner implements ResourceOwnerInterface {
                public function getId(): string
                {
                    return $this->get('id');
                }
            }
        );

        $sut = new CognitoAdapter($mockClient);

        // Execute
        $result = $sut->doesUserExist($username);

        // Assert
        static::assertTrue($result);
    }

    /**
     * @test
     */
    public function doesUserExist_ReturnsFalse_WhenUserDoesNotExistInCognito():void
    {
        $username = 'user4574';

        // Setup
        $previousException = m::mock(AwsException::class);
        $previousException->expects('getAwsErrorCode')->andReturn(CognitoAdapter::EXCEPTION_USER_NOT_FOUND);
        $exception = new ClientException('null', 0, $previousException);

        $mockClient = m::mock(Client::class);
        $mockClient->shouldReceive('getUserByIdentifier')->with($username)->once()->andThrow($exception);

        $sut = new CognitoAdapter($mockClient);

        // Execute
        $result = $sut->doesUserExist($username);

        // Assert
        static::assertFalse($result);
    }

    /**
     * @test
     */
    public function doesUserExist_BubblesUnexpectedException_WhenClientThrowsUnexpectedException(): void
    {
        $username = 'user4574';

        // Setup
        $previousException = m::mock(AwsException::class);
        $previousException->expects('getAwsErrorCode')->andReturn('SomeOtherThing');
        $exception = new ClientException('null', 0, $previousException);

        $mockClient = m::mock(Client::class);
        $mockClient->shouldReceive('getUserByIdentifier')->with($username)->once()->andThrow($exception);

        $sut = new CognitoAdapter($mockClient);

        $this->expectException(ClientException::class);

        // Execute
        $sut->doesUserExist($username);
    }

    /**
     * @test
     * @dataProvider dataProvider_getIdentityStrings
     * @param string $identity
     */
    public function getIdentity_resultDoesNotContainUppercaseAndCaseConvertsToLowercase(string $identity): void
    {
        $mockClient = m::mock(Client::class);
        $sut = new CognitoAdapter($mockClient);
        $sut->setIdentity($identity);

        $this->assertDoesNotMatchRegularExpression('/[A-Z]+/', $sut->getIdentity());
    }

    public function dataProvider_getIdentityStrings(): array
    {
        return [
            'Lowercase' => ['testing'],
            'Mixed Case' => ['tEsTiNG'],
            'Uppercase' => ['TESTING'],
            'Uppercase With Special Characters' => ['TESTING@"'],
        ];
    }
}
