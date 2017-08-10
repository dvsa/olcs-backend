<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Organisation;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Organisation dashboard
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class Dashboard extends AbstractQueryHandler
{
    protected $repoServiceName = 'Organisation';

    protected $extraRepos = ['Correspondence', 'Fee', 'Application'];

    /**
     * Handle query
     *
     * @param \Dvsa\Olcs\Transfer\Query\Organisation\Dashboard $query Query
     *
     * @return \Dvsa\Olcs\Api\Domain\QueryHandler\Result
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var Entity\Organisation\Organisation $organisation */
        $organisation =  $this->getRepo()->fetchUsingId($query);

        list($licences, $applications, $variations) = $this->filter($organisation);

        return $this->result(
            $organisation,
            [],
            [
                'dashboard' => [
                    'licences' => $this->resultList(
                        $licences,
                        [
                            'licenceType',
                            'status',
                            'trafficArea',
                            'isExpired',
                            'isExpiring',
                        ]
                    ),
                    'applications' => $this->resultList(
                        $applications,
                        [
                            'licenceType',
                            'status',
                            'licence',
                        ]
                    ),
                    'variations' => $this->resultList(
                        $variations,
                        [
                            'licenceType',
                            'status',
                            'licence',
                        ]
                    ),
                    'correspondenceCount' => $this->getCorrespondenceCount($organisation->getId()),
                    'feeCount' => $this->getFeeCount($organisation->getId()),
                ],
            ]
        );
    }

    /**
     * Filter
     *
     * @param Entity\Organisation\Organisation $organisation Organisation entity
     *
     * @return array (licences, applications, variations)
     */
    protected function filter($organisation)
    {
        /** @var Repository\Application $repo */
        $repo = $this->getRepo('Application');

        $licences = $organisation->getActiveLicences();

        $applications = $repo->fetchByOrgAndStatusForActiveLicences(
            $organisation->getId(),
            [
                ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION,
                ApplicationEntity::APPLICATION_STATUS_GRANTED,
                ApplicationEntity::APPLICATION_STATUS_NOT_SUBMITTED,
            ]
        );

        $variations = [];
        $newApplications = [];

        /** @var Entity\Application\Application $application */
        foreach ($applications as $application) {
            if ($application->isVariation()) {
                $variations[] = $application;
            } else {
                $newApplications[] = $application;
            }
        }

        return [$licences, $newApplications, $variations];
    }

    /**
     * Get Correspondence Count
     *
     * @param int $organisationId Organisation Id
     *
     * @return int
     */
    protected function getCorrespondenceCount($organisationId)
    {
        /** @var Repository\Correspondence $repo */
        $repo = $this->getRepo('Correspondence');

        return $repo->getUnreadCountForOrganisation($organisationId);
    }

    /**
     * Get Count of Fees
     *
     * @param int $organisationId Organisation Id
     *
     * @return int
     */
    protected function getFeeCount($organisationId)
    {
        /** @var Repository\Fee $repo */
        $repo = $this->getRepo('Fee');

        return $repo->getOutstandingFeeCountByOrganisationId($organisationId, true);
    }
}
