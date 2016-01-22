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
        $applicationData = [];
        $licenceData = [];
        $historicTm = null;

        /** @var \ArrayIterator $results */
        $results = $repo->fetchList($query);

        if (!empty($results)) {
            /** @var HistoricTmEntity $historicTm */
            $historicTm = $repo->fetchById($results[0]['id']);
            if ($historicTm instanceof HistoricTmEntity) {
                $applicationData = $this->generateApplicationData($results);
                $licenceData = $this->generateLicenceData($results);
            }
        }

        return $this->result(
            $historicTm,
            [],
            [
                'applicationData' => $applicationData,
                'licenceData' => $licenceData
            ]
        );
    }

    /**
     * Extract application data from each result
     *
     * @param $results
     *
     * @return array
     */
    private function generateApplicationData($results)
    {
        $applicationData = [];
        $index = 0;
        foreach ($results as $result) {
            if (!empty($result['applicationId']) && $result['licOrApp'] === self::APPLICATION_FLAG) {
                $applicationData[$index]['licNo'] = $result['licNo'];
                $applicationData[$index]['applicationId'] = $result['applicationId'];
                $applicationData[$index]['seenContract'] = $result['seenContract'];
                $applicationData[$index]['seenQualification'] = $result['seenQualification'];
                $applicationData[$index]['hoursPerWeek'] = $result['hoursPerWeek'];
                $applicationData[$index]['dateAdded'] = $result['dateAdded'];
                $applicationData[$index]['dateRemoved'] = $result['dateRemoved'];

                $index++;
            }
        }
        return $applicationData;
    }

    /**
     * Extract licence data from each result
     *
     * @param $results
     *
     * @return array
     */
    private function generateLicenceData($results)
    {
        $licenceData = [];
        $index = 0;
        foreach ($results as $result) {
            if (!empty($result['licNo']) && $result['licOrApp'] === self::LICENCE_FLAG) {
                $licenceData[$index]['licNo'] = $result['licNo'];
                $licenceData[$index]['licenceOrApp'] = $result['licOrApp'];
                $licenceData[$index]['seenContract'] = $result['seenContract'];
                $licenceData[$index]['seenQualification'] = $result['seenQualification'];
                $licenceData[$index]['hoursPerWeek'] = $result['hoursPerWeek'];
                $licenceData[$index]['dateAdded'] = $result['dateAdded'];
                $licenceData[$index]['dateRemoved'] = $result['dateRemoved'];

                $index++;
            }
        }

        return $licenceData;
    }
}
