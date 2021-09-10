<?php

namespace Dvsa\Olcs\Api\Rbac;

use Dvsa\Contracts\Auth\Exceptions\InvalidTokenException;
use Dvsa\Contracts\Auth\OAuthClientInterface;
use Dvsa\Olcs\Api\Domain\Exception\BadRequestException;
use Dvsa\Olcs\Api\Domain\Exception\HeaderNotFoundException;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository\User as UserRepository;
use Dvsa\Olcs\Api\Entity\User\User;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Request;
use Laminas\Stdlib\RequestInterface;
use ZfcRbac\Identity\IdentityInterface;

/**
 * @see JWTIdentityProviderFactory
 */
class JWTIdentityProvider implements IdentityProviderInterface
{
    use IdentityProviderTrait;

    const HEADER_NAME = 'Authorization';

    const MESSAGE_MALFORMED_BEARER = 'Malformed Bearer token';
    const MESSAGE_HEADER_MISSING = 'Authorization header is missing';

    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Identity;
     */
    private $identity;

    /**
     * @var OAuthClientInterface
     */
    private $client;

    public function __construct(UserRepository $repository, RequestInterface $request, OAuthClientInterface $client)
    {
        $this->repository = $repository;
        $this->request = $request;
        $this->client = $client;
    }

    /**
     * Get the identity
     *
     * @return IdentityInterface
     * @throws BadRequestException
     * @throws InvalidTokenException
     * @throws HeaderNotFoundException
     * @throws NotFoundException
     */
    public function getIdentity(): IdentityInterface
    {
        if (!is_null($this->identity)) {
            return $this->identity;
        }

        if ($this->request instanceof \Laminas\Console\Request) {
            return $this->identity = new Identity($this->getSystemUser());
        }

        if (empty($header = $this->request->getHeader(static::HEADER_NAME))) {
            throw new HeaderNotFoundException(static::MESSAGE_HEADER_MISSING);
        }

        $decodedToken = $this->getJWT($header);
        $user = $this->repository->fetchEnabledIdentityByLoginId($decodedToken['username']);

        if (is_null($user)) {
            return $this->identity = new Identity(User::anon());
        }

        return $this->identity = new Identity($user);
    }

    /**
     * @return string
     */
    public function getHeaderName(): string
    {
        return static::HEADER_NAME;
    }

    /**
     * @return mixed
     * @throws NotFoundException
     */
    private function getSystemUser()
    {
        $auth = IdentityProviderInterface::SYSTEM_USER;
        return $this->repository->fetchById($auth);
    }

    /**
     * @param HeaderInterface $header
     * @return array
     * @throws BadRequestException
     * @throws InvalidTokenException
     */
    private function getJWT(HeaderInterface $header): array
    {
        if (!preg_match('/Bearer\s((.*)\.(.*)\.(.*))/', $header->getFieldValue())) {
            throw new BadRequestException(static::MESSAGE_MALFORMED_BEARER);
        }

        $token = substr($header->getFieldValue(), 7);

        return $this->client->decodeToken($token);
    }
}
