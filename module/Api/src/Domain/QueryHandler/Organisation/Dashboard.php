<?php

/**
 * Organisation dashboard
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Organisation;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Organisation dashboard
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class Dashboard extends AbstractQueryHandler
{
    protected $repoServiceName = 'Organisation';

    protected $extraRepos = ['Correspondence', 'Fee'];

    public function handleQuery(QueryInterface $query)
    {
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
     * @param OrganisationEntity
     * @return array (licences, applications, variations)
     */
    protected function filter($organisation)
    {
        /**
         * Restrict the types of licence we display
         */
        $displayLicenceStatus = [
            LicenceEntity::LICENCE_STATUS_VALID,
            LicenceEntity::LICENCE_STATUS_CURTAILED,
            LicenceEntity::LICENCE_STATUS_SUSPENDED
        ];

        /**
         * Restrict the types of applications / variations we display
         */
        $displayApplicationStatus = [
            ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION,
            ApplicationEntity::APPLICATION_STATUS_GRANTED,
            ApplicationEntity::APPLICATION_STATUS_NOT_SUBMITTED
        ];

        $applications = [];
        $variations = [];
        $licences = [];

        if (!empty($organisation->getLicences())) {

            foreach ($organisation->getLicences() as $licence) {

                if (in_array($licence->getStatus()->getId(), $displayLicenceStatus)) {
                    $licences[$licence->getId()] = $licence;
                }

                foreach ($licence->getApplications() as $application) {
                    if (in_array($application->getStatus()->getId(), $displayApplicationStatus)) {
                        if ($application->isVariation()) {
                            $variations[$application->getId()] = $application;
                        } else {
                            $applications[$application->getId()] = $application;
                        }
                    }
                }
            }
        }

        return array($licences, $applications, $variations);
    }

    /**
     * @param int $organisationId
     * @return int
     */
    protected function getCorrespondenceCount($organisationId)
    {
        return $this->getRepo('Correspondence')->getUnreadCountForOrganisation($organisationId);
    }

    /**
     * @param int $organisationId
     * @return int
     */
    protected function getFeeCount($organisationId)
    {
        return $this->getRepo('Fee')->getOutstandingFeeCountByOrganisationId($organisationId);
    }
}
