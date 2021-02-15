<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\MyAccount;

use Dvsa\Olcs\Api\Domain\CacheAwareInterface;
use Dvsa\Olcs\Api\Domain\CacheAwareTrait;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\SystemParameter;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Olcs\Logging\Log\Logger;

/**
 * MyAccount
 */
class MyAccount extends AbstractQueryHandler implements CacheAwareInterface
{
    use CacheAwareTrait;

    protected $extraRepos = ['SystemParameter'];

    /**
     * Handle my account query
     *
     * @param QueryInterface $query Query
     *
     * @return \Dvsa\Olcs\Api\Domain\QueryHandler\Result
     * @throws NotFoundException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function handleQuery(QueryInterface $query)
    {
        $user = $this->getCurrentUser();

        if ($user === null) {
            throw new NotFoundException('No user currently logged in');
        }

        $userId = $user->getId();

        if ($userId === null) {
            $userId = 'anon';
        }

        if ($this->cacheService->hasCustomItem(CacheEncryption::USER_ACCOUNT_IDENTIFIER, $userId)) {
            Logger::debug('retrieving user account for ' . $userId . ' from cache');
            return $this->cacheService->getCustomItem(CacheEncryption::USER_ACCOUNT_IDENTIFIER, $userId);
        }

        /** @var SystemParameter $systemParameterRepo */
        $systemParameterRepo = $this->getRepo('SystemParameter');
        $isEligibleForPermits = $user->isEligibleForPermits();

        $result = $this->result(
            $user,
            [
                'team',
                'transportManager',
                'partnerContactDetails',
                'localAuthority',
                'contactDetails' => [
                    'person' => ['title'],
                    'address' => ['countryCode'],
                    'phoneContacts' => ['phoneContactType']
                ],
                'organisationUsers' => [
                    'organisation',
                ],
                'roles' => ['role']
            ],
            [
                'hasActivePsvLicence' => $user->hasActivePsvLicence(),
                'numberOfVehicles' => $user->getNumberOfVehicles(),
                'eligibleForPermits' => $isEligibleForPermits,
                'eligibleForPrompt' => $isEligibleForPermits && $systemParameterRepo->isSelfservePromptEnabled(),
            ]
        );

        $this->cacheService->setCustomItem(CacheEncryption::USER_ACCOUNT_IDENTIFIER, $result->serialize(), $userId);
        return $result;
    }
}
