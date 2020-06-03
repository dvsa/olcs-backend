<?php

/**
 * Licence
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Licence;

use Dvsa\Olcs\Api\Domain\LicenceStatusAwareTrait;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;

/**
 * Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class Licence extends AbstractQueryHandler
{
    use LicenceStatusAwareTrait;

    protected $repoServiceName = 'Licence';

    protected $extraRepos = ['ContinuationDetail', 'Note', 'SystemParameter', 'Application'];

    /**
     * @var \Dvsa\Olcs\Api\Service\Lva\SectionAccessService
     */
    private $sectionAccessService;

    /**
     * Factory
     *
     * @param ServiceLocatorInterface $serviceLocator Service manager
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->sectionAccessService = $mainServiceLocator->get('SectionAccessService');

        return parent::createService($serviceLocator);
    }

    /**
     * Handle query
     *
     * @param QueryInterface $query DTO
     *
     * @return \Dvsa\Olcs\Api\Domain\QueryHandler\Result
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var Entity\Licence\Licence $licence */
        $licence = $this->getRepo()->fetchUsingId($query);

        $this->guardAgainstLackOfPermission($licence);

        $this->auditRead($licence);

        $continuationDetail = $this->getContinuationDetail($licence);
        $continuationDetailResponse = ($continuationDetail) ?
            $this->result($continuationDetail, ['continuation', 'licence'])->serialize() :
            null;
        $latestNote = $this->getRepo('Note')->fetchForOverview($query->getId());

        $isLicenceSurrenderAllowed = $this->doesLicenceApplicationsHaveCorrectStatusForSurrender($query)
            && $this->isLicenceStatusSurrenderable($licence);

        $showExpiryWarning = $continuationDetail !== null
            && $licence->isExpiring()
            && !$this->getRepo('SystemParameter')->getDisabledDigitalContinuations()
            && (string)$continuationDetail->getStatus() === Entity\Licence\ContinuationDetail::STATUS_PRINTED;

        return $this->result(
            $licence,
            [
                'isExpired',
                'isExpiring',
                'cases' => [
                    'appeal' => [
                        'outcome',
                        'reason',
                    ],
                    'stays' => [
                        'stayType',
                        'outcome',
                    ],
                ],
                'correspondenceCd' => [
                    'address',
                ],
                'status',
                'goodsOrPsv',
                'licenceType',
                'trafficArea',
                'organisation' => [
                    'organisationPersons' => [
                        'person' => ['disqualifications']
                    ],
                    'tradingNames',
                    'type',
                    'disqualifications',
                ],
                'licenceStatusRules' => ['licenceStatus'],
                'applications',
            ],
            [
                'sections' => $this->sectionAccessService->getAccessibleSectionsForLicence($licence),
                'niFlag' => $licence->getNiFlag(),
                'isMlh' => $licence->getOrganisation()->isMlh(),
                'continuationMarker' => $continuationDetailResponse,
                'latestNote' => $latestNote,
                'canHaveInspectionRequest' => !$licence->isSpecialRestricted(),
                'showExpiryWarning' => $showExpiryWarning,
                'isLicenceSurrenderAllowed' => $isLicenceSurrenderAllowed,
            ]
        );
    }

    /**
     * Get a Continuation Detail for the marker
     *
     * @param \Dvsa\Olcs\Api\Entity\Licence\Licence $licence Licence
     *
     * @return \Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail|null
     */
    private function getContinuationDetail(\Dvsa\Olcs\Api\Entity\Licence\Licence $licence)
    {
        $continuationDetails = $this->getRepo('ContinuationDetail')->fetchForLicence($licence->getId());
        if (count($continuationDetails) > 0) {
            return $continuationDetails[0];
        }

        return null;
    }

    private function guardAgainstLackOfPermission(Entity\Licence\Licence $licence) : void
    {
        if ($this->isExternalUser() && !$this->isLicenceStatusAccessibleForExternalUser($licence)) {
            throw new ForbiddenException('You do not have permission to access this record');
        }
    }

    /**
     * @param QueryInterface $query
     *
     * @return bool
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function doesLicenceApplicationsHaveCorrectStatusForSurrender($query): bool
    {
        /** @var \Dvsa\Olcs\Api\Domain\Repository\Application $applications */
        $applications = $this->getRepo('Application');
        return empty($applications->fetchOpenApplicationsForLicence($query->getId()));
    }
}
