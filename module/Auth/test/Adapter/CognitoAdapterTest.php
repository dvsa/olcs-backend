<?php
declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Test\Adapter;

use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;
use Aws\CommandInterface;
use Aws\Exception\AwsException;
use Dvsa\Authentication\Cognito\Client;
use Dvsa\Contracts\Auth\AccessTokenInterface;
use Dvsa\Contracts\Auth\Exceptions\ChallengeException;
use Dvsa\Contracts\Auth\Exceptions\ClientException;
use Dvsa\Contracts\Auth\Exceptions\InvalidTokenException;
use Dvsa\Contracts\Auth\ResourceOwnerInterface;
use Dvsa\Olcs\Auth\Adapter\CognitoAdapter;
use Dvsa\Olcs\Auth\Exception\ChangePasswordException;
use Dvsa\Olcs\Auth\Exception\ResetPasswordException;
use Dvsa\Olcs\Transfer\Result\Auth\ChangeExpiredPasswordResult;
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
     */
    public function authenticate_ReturnsFailureIdentityNotFoundResult_WhenClientExceptionIsThrown_WithUserNotFound()
    {
        // Setup
        $mockClient = m::mock(Client::class);
        $mockClient->shouldReceive('authenticate')->andThrow(
            new ClientException(
                '',
                0,
                new CognitoIdentityProviderException(
                    '',
                    m::mock(CommandInterface::class),
                    [
                        'code' => CognitoAdapter::USER_NOT_FOUND
                    ]
                )
            )
        );

        $sut = new CognitoAdapter($mockClient);
        $sut->setIdentity('identity');
        $sut->setCredential('credential');

        // Execute
        $result = $sut->authenticate();

        // Assert
        static::assertEquals(Result::FAILURE_IDENTITY_NOT_FOUND, $result->getCode());
    }

    /**
     * @test
     */
    public function authenticate_ReturnsFailureCredentialInvalidResult_WhenClientExceptionIsThrown_WithInvalidPassword()
    {
        // Setup
        $mockClient = m::mock(Client::class);
        $mockClient->shouldReceive('authenticate')->andThrow(
            new ClientException(
                CognitoAdapter::MESSAGE_INCORRECT_USERNAME_OR_PASSWORD,
                0,
                new CognitoIdentityProviderException(
                    '',
                    m::mock(CommandInterface::class),
                    [
                        'code' => CognitoAdapter::AWS_ERROR_NOT_AUTHORIZED,
                    ]
                )
            )
        );

        $sut = new CognitoAdapter($mockClient);
        $sut->setIdentity('identity');
        $sut->setCredential('credential');

        // Execute
        $result = $sut->authenticate();

        // Assert
        static::assertEquals(Result::FAILURE_CREDENTIAL_INVALID, $result->getCode());
    }

    /**
     * @test
     */
    public function authenticate_ReturnsFailureAccountDisabledResult_WhenClientExceptionIsThrown_WithDisabledAccount()
    {
        // Setup
        $mockClient = m::mock(Client::class);
        $mockClient->shouldReceive('authenticate')->andThrow(
            new ClientException(
                CognitoAdapter::MESSAGE_USER_IS_DISABLED,
                0,
                new CognitoIdentityProviderException(
                    '',
                    m::mock(CommandInterface::class),
                    [
                        'code' => CognitoAdapter::AWS_ERROR_NOT_AUTHORIZED,
                    ]
                )
            )
        );

        $sut = new CognitoAdapter($mockClient);
        $sut->setIdentity('identity');
        $sut->setCredential('credential');

        // Execute
        $result = $sut->authenticate();

        // Assert
        static::assertEquals(CognitoAdapter::FAILURE_ACCOUNT_DISABLED, $result->getCode());
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
     * @dataProvider clientExceptionDataProvider
     */
    public function changedExpiredPassword_ReturnsExpectedResult_WhenClientExceptionIsThrown(string $awsErrorCode, int $expectedResultCode)
    {
        // Setup
        $previousException = m::mock(AwsException::class);
        $previousException->expects('getAwsErrorCode')->andReturn($awsErrorCode);

        $mockClient = m::mock(Client::class);
        $mockClient->shouldReceive('responseToAuthChallenge')
            ->andThrow(new ClientException('null', 0, $previousException));

        $sut = new CognitoAdapter($mockClient);

        // Execute
        $result = $sut->changeExpiredPassword('newPassword', 'challengeSession', 'username');

        // Assert
        static::assertEquals($expectedResultCode, $result->getCode());
    }

    public function clientExceptionDataProvider(): array
    {
        return [
            'Invalid Password' => [CognitoAdapter::AWS_ERROR_INVALID_PASSWORD, ChangeExpiredPasswordResult::FAILURE_NEW_PASSWORD_INVALID],
            'Unauthorised' => [CognitoAdapter::AWS_ERROR_NOT_AUTHORIZED, ChangeExpiredPasswordResult::FAILURE_NOT_AUTHORIZED],
            'Generic' => ['generic', ChangeExpiredPasswordResult::FAILURE]
        ];
    }

    /**
     * @test
     * @dataProvider dpChangePasswordException
     */
    public function resetPasswordWithException(string $exceptionClass): void
    {
        $exceptionMessage = 'exception message';
        $this->expectException(ResetPasswordException::class);
        $this->expectExceptionMessage($exceptionMessage);

        $identifier = 'identifier';
        $newPassword = 'new password';

        $mockClient = m::mock(Client::class);
        $mockClient->expects('changePassword')
            ->with($identifier, $newPassword, true)
            ->andThrow($exceptionClass, $exceptionMessage);

        $sut = new CognitoAdapter($mockClient);

        $sut->resetPassword($identifier, $newPassword);
    }

    /**
     * @test
     * @dataProvider dpChangePasswordException
     */
    public function changePasswordWithException(string $exceptionClass): void
    {
        $exceptionMessage = 'exception message';
        $this->expectException(ChangePasswordException::class);
        $this->expectExceptionMessage($exceptionMessage);

        $identifier = 'identifier';
        $oldPassword = 'old password';
        $newPassword = 'new password';

        $mockClient = m::mock(Client::class);
        $mockClient->expects('changePassword')
            ->with($identifier, $newPassword)
            ->andThrow($exceptionClass, $exceptionMessage);

        $sut = new CognitoAdapter($mockClient);

        $sut->changePassword($identifier, $oldPassword, $newPassword);
    }

    public function dpChangePasswordException(): array
    {
        return [
            [ClientException::class],
            [\Exception::class]
        ];
    }

    /**
     * @test
     */
    public function resetPasswordNoException(): void
    {
        $identifier = 'identifier';
        $newPassword = 'new password';
        $expectedResult = true;

        $mockClient = m::mock(Client::class);
        $mockClient->expects('changePassword')
            ->with($identifier, $newPassword, true)
            ->andReturn($expectedResult);

        $sut = new CognitoAdapter($mockClient);

        static::assertEquals($expectedResult, $sut->resetPassword($identifier, $newPassword));
    }

    /**
     * @test
     * @dataProvider dpChangePasswordNoException
     */
    public function changePasswordNoException(bool $changeResult, int $statusCode): void
    {
        $identifier = 'identifier';
        $oldPassword = 'old password';
        $newPassword = 'new password';
        $expectedResult = ['status' => $statusCode];

        $mockClient = m::mock(Client::class);
        $mockClient->expects('changePassword')
            ->with($identifier, $newPassword)
            ->andReturn($changeResult);

        $sut = new CognitoAdapter($mockClient);

        // Assert
        static::assertEquals($expectedResult, $sut->changePassword($identifier, $oldPassword, $newPassword));
    }

    public function dpChangePasswordNoException(): array
    {
        return [
            [true, 200],
            [false, 500],
        ];
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
}
