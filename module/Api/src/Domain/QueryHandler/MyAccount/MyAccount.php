<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\MyAccount;

use Dvsa\Olcs\Api\Domain\CacheAwareInterface;
use Dvsa\Olcs\Api\Domain\CacheAwareTrait;
use Dvsa\Olcs\Api\Entity\System\SystemParameter;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Olcs\Logging\Log\Logger;

/**
 * MyAccount
 */
class MyAccount extends AbstractQueryHandler implements CacheAwareInterface
{
    use CacheAwareTrait;

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

        $isEligibleForPermits = false;
        $isEligibleForPrompt = false;
        $hasActivePsv = false;
        $hasSubmittedLicenceApplication = false;
        $numVehicles = 0;

        $dataAccess = [];
        $isInternal = $user->isInternal();

        if ($isInternal) {
            $teamDataExclusionsParam = $this->getCacheById(
                CacheEncryption::SYS_PARAM_IDENTIFIER,
                SystemParameter::DATA_SEPARATION_TEAMS_EXEMPT
            );

            $teamsExcluded = explode(",", $teamDataExclusionsParam->getObject()->getParamValue());

            $team = $user->getTeam();

            $dataAccess = [
                'canAccessAll' => $team->canAccessAllData($teamsExcluded),
                'canAccessGb' => $team->canAccessGbData($teamsExcluded),
                'canAccessNi' => $team->canAccessNiData($teamsExcluded),
                'trafficAreas' => $team->getAllowedTrafficAreas($teamsExcluded),
            ];
        } else {
            $isEligibleForPermits = $user->isEligibleForPermits();

            if ($isEligibleForPermits) {
                $selfservePrompt = $this->getCacheById(
                    CacheEncryption::SYS_PARAM_IDENTIFIER,
                    SystemParameter::ENABLE_SELFSERVE_PROMPT
                );

                $isEligibleForPrompt = $selfservePrompt->getObject()->getParamValue();
            }

            $hasActivePsv = $user->hasActivePsvLicence();
            $numVehicles = $user->getNumberOfVehicles();
            $hasSubmittedLicenceApplication = $user->hasOrganisationSubmittedLicenceApplication();
        }

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
                'hasActivePsvLicence' => $hasActivePsv,
                'numberOfVehicles' => $numVehicles,
                'hasOrganisationSubmittedLicenceApplication' => $hasSubmittedLicenceApplication,
                'eligibleForPermits' => $isEligibleForPermits,
                'eligibleForPrompt' => $isEligibleForPrompt,
                'dataAccess' => $dataAccess,
                'isInternal' => $isInternal,
            ]
        );

        $this->cacheService->setCustomItem(CacheEncryption::USER_ACCOUNT_IDENTIFIER, $result->serialize(), $userId);
        return $result;
    }
}
