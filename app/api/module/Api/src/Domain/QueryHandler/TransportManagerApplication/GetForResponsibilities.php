<?php

/**
 * Get a Transport Manager Application for Responsibilities page
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\TransportManagerApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Get a Transport Manager Application for Responsibilities page
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class GetForResponsibilities extends AbstractQueryHandler
{
    protected $repoServiceName = 'TransportManagerApplication';

    protected $extraRepos = [
        'OtherLicence'
    ];

    public function handleQuery(QueryInterface $query)
    {
        $transportManagerApplication = $this->getRepo()->fetchForResponsibilities($query->getId());

        return $this->result(
            $transportManagerApplication,
            [
                'application' => [
                    'licence' => [
                        'organisation'
                    ]
                ],
                'operatingCentres',
                'otherLicences' => [
                    'role'
                ]
            ]
        );
    }
}
