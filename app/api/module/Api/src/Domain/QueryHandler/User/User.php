<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\User;

use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareInterface;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareTrait;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\EventHistory\EventHistoryType;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Api\Rbac\PidIdentityProvider;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * User
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class User extends AbstractQueryHandler implements OpenAmUserAwareInterface
{
    use OpenAmUserAwareTrait;

    protected $repoServiceName = 'User';

    protected $extraRepos = ['EventHistory', 'EventHistoryType'];

    private array $config;

    /**
     * Handle query
     *
     * @param QueryInterface $query query
     *
     * @return \Dvsa\Olcs\Api\Domain\QueryHandler\Result
     */
    public function handleQuery(QueryInterface $query)
    {
        if (!$this->isGranted(Permission::CAN_MANAGE_USER_INTERNAL)) {
            throw new ForbiddenException('You do not have permission to manage the record');
        }

        $user = $this->getRepo()->fetchUsingId($query);

        // get user's latest password reset event
        $passwordResetEvents = $this->getRepo('EventHistory')
            ->fetchByAccount(
                $user->getId(),
                $this->getRepo('EventHistoryType')
                    ->fetchOneByEventCode(EventHistoryType::EVENT_CODE_PASSWORD_RESET),
                'id',
                'desc',
                1
            );

        $lockedOn = null;
        if ($this->config['auth']['identity_provider'] === PidIdentityProvider::class) {
            $authDetails = $this->getOpenAmUser()->fetchUser($user->getPid());
            if (!empty($authDetails['meta']['locked'])) {
                $lockedOn = \DateTime::createFromFormat('YmdHis.uT', $authDetails['meta']['locked'])
                    ->format(\DateTime::W3C);
            }
        }

        return $this->result(
            $user,
            [
                'team',
                'transportManager' => [
                    'homeCd' => [
                        'person'
                    ],
                ],
                'localAuthority',
                'partnerContactDetails',
                'roles',
                'contactDetails' => [
                    'person',
                    'address' => ['countryCode'],
                    'phoneContacts' => ['phoneContactType']
                ],
                'organisationUsers' => [
                    'organisation',
                ],
            ],
            [
                'userType' => $user->getUserType(),
                'lastLoggedInOn' => $user->getLastLoginAt() ?? null,
                'lockedOn' => $lockedOn,
                'latestPasswordResetEvent'
                    => !empty($passwordResetEvents)
                        ? array_shift($passwordResetEvents)->serialize()
                        : null,
            ]
        );
    }

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return User
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $fullContainer = $container;

        $this->config = $container->get('Config');
        return parent::__invoke($fullContainer, $requestedName, $options);
    }
}
