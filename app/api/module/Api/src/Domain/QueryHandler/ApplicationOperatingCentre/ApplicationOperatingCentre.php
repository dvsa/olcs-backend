<?php

/**
 * Application Operating Centre
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\ApplicationOperatingCentre;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre as ApplicationOperatingCentreEntity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Application Operating Centre
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationOperatingCentre extends AbstractQueryHandler
{
    protected $repoServiceName = 'ApplicationOperatingCentre';

    public function handleQuery(QueryInterface $query)
    {
        /** @var ApplicationOperatingCentreEntity $aoc */
        $aoc = $this->getRepo()->fetchUsingId($query);

        $application = $aoc->getApplication();

        return $this->result(
            $aoc,
            [
                'operatingCentre' => [
                    'address' => [
                        'countryCode'
                    ],
                    'adDocuments'
                ]
            ],
            [
                'isPsv' => $application->isPsv(),
                'canUpdateAddress' => $application->isNew(),
                'wouldIncreaseRequireAdditionalAdvertisement' => $application->isVariation()
            ]
        );
    }
}
