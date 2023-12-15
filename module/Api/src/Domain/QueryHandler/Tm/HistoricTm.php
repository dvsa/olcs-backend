<?php

/**
 * Historic TM
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Tm;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\HistoricTm as HistoricTmRepo;
use Dvsa\Olcs\Api\Entity\Tm\HistoricTm as HistoricTmEntity;

/**
 * Historic TM
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class HistoricTm extends AbstractQueryHandler
{
    const LICENCE_FLAG = 'L';
    const APPLICATION_FLAG = 'A';

    protected $repoServiceName = 'HistoricTm';

    /**
     * Handle HistoricTm query. HistoricTms data contains unnormalised application and licence information that
     * DOES NOT neccesarily relate to other entities in the database and are no relationships defined on the table.
     * Hence we do not interface with other entities, but merely extract the data as is.
     *
     * @param QueryInterface $query
     * @return \Dvsa\Olcs\Api\Domain\QueryHandler\Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleQuery(QueryInterface $query)
    {
        /* @var $repo HistoricTmRepo */
        $repo = $this->getRepo();
        $additionalData = ['applications', 'licences'];
        $historicTm = null;

        /** @var \ArrayIterator $results */
        $results = $repo->fetchList($query);

        if (!empty($results)) {
            /** @var HistoricTmEntity $historicTm */
            $historicTm = $repo->fetchById($results[0]['id']);
            $additionalData = $this->generateAdditionalData($results);
        }

        return $this->result(
            $historicTm,
            [],
            [
                'applicationData' => $additionalData['applications'],
                'licenceData' => $additionalData['licences']
            ]
        );
    }

    /**
     * Extract application and licence data from each result
     *
     * @param $results
     *
     * @return array
     */
    private function generateAdditionalData($results)
    {
        $applicationData = [];
        $licenceData = [];
        $appIndex = 0;
        $licIndex = 0;
        foreach ($results as $result) {
            if ($result['licOrApp'] === self::APPLICATION_FLAG) {
                $applicationData[$appIndex]['licNo'] = $result['licNo'];
                $applicationData[$appIndex]['applicationId'] = $result['applicationId'];
                $applicationData[$appIndex]['seenContract'] = $result['seenContract'];
                $applicationData[$appIndex]['seenQualification'] = $result['seenQualification'];
                $applicationData[$appIndex]['hoursPerWeek'] = $result['hoursPerWeek'];
                $applicationData[$appIndex]['dateAdded'] = $result['dateAdded'];
                $applicationData[$appIndex]['dateRemoved'] = $result['dateRemoved'];
                $appIndex++;
            } else {
                $licenceData[$licIndex]['licNo'] = $result['licNo'];
                $licenceData[$licIndex]['licenceOrApp'] = $result['licOrApp'];
                $licenceData[$licIndex]['seenContract'] = $result['seenContract'];
                $licenceData[$licIndex]['seenQualification'] = $result['seenQualification'];
                $licenceData[$licIndex]['hoursPerWeek'] = $result['hoursPerWeek'];
                $licenceData[$licIndex]['dateAdded'] = $result['dateAdded'];
                $licenceData[$licIndex]['dateRemoved'] = $result['dateRemoved'];
                $licIndex++;
            }
        }

        return ['applications' => $applicationData, 'licences' => $licenceData];
    }
}
