<?php

/**
 * Note
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Processing;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\Note as NoteRepository;
use Dvsa\Olcs\Transfer\Query\Processing\NoteList as NoteListQuery;
use Doctrine\ORM\Query;

/**
 * Note
 *
 * @todo IMO this should be split into 3 different query handlers, I think this one is doing a little too much
 */
class NoteList extends AbstractQueryHandler
{
    protected $repoServiceName = 'Note';
    protected $extraRepos = ['Cases', 'Bus', 'Application'];

    public function handleQuery(QueryInterface $query)
    {
        /* @var \Dvsa\Olcs\Transfer\Query\Processing\NoteList $query */

        // The case / licence business logic.
        if (null !== $query->getCase()) {

            $caseId = $query->getCase();

            /* @var \Dvsa\Olcs\Api\Entity\Cases\Cases $case */
            $caseRepo = $this->getRepo('Cases');
            $case = $caseRepo->fetchById($caseId);

            if ($case->getLicence() !== null) {

                $data = $query->getArrayCopy();

                $licenceId = $case->getLicence()->getId();

                // Get all cases for that licence
                $caseIds = [];
                $cases = $case->getLicence()->getCases();
                foreach ($cases as $case) {
                    $caseIds[] = $case->getId();
                }

                $data['casesMultiple'] = $caseIds;

                if (isset($data['case'])) {
                    unset($data['case']);
                }

                $data['licence'] = $licenceId;

                // Replace existing
                $query = NoteListQuery::create($data);
            }
        }

        // The bus reg business logic.
        if (null !== $query->getBusReg()) {

            $busRegId = $query->getBusReg();

            /* @var \Dvsa\Olcs\Api\Entity\Bus\BusReg $busReg */
            $busReg = $this->getRepo('Bus')->fetchById($busRegId);

            if ($busReg->getLicence() !== null) {

                $data = $query->getArrayCopy();
                $data['licence'] = $busReg->getLicence()->getId();

                $query = NoteListQuery::create($data);
            }
        }

        // The application business logic.
        if (null !== $query->getApplication()) {

            /** @var \Dvsa\Olcs\Transfer\Query\Processing\NoteList $query */
            $applicationId = $query->getApplication();
            /* @var \Dvsa\Olcs\Api\Entity\Application\Application $application */
            $application = $this->getRepo('Application')->fetchById($applicationId);

            if ($application->getLicence() !== null) {

                $data = $query->getArrayCopy();
                $data['licence'] = $application->getLicence()->getId();

                $query = NoteListQuery::create($data);
            }
        }

        /** @var NoteRepository $repo */
        $repo = $this->getRepo();
        $repo->disableSoftDeleteable();

        $data = $query->getArrayCopy();

        unset($data['noteType']);

        $unfilteredQuery = \Dvsa\Olcs\Transfer\Query\Processing\NoteList::create($data);

        return [
            'result' => $this->resultList(
                $repo->fetchList($query, Query::HYDRATE_OBJECT),
                [
                    'noteType',
                    'case',
                    'application',
                    'busReg',
                    'createdBy' => [
                        'contactDetails' => [
                            'person'
                        ]
                    ]
                ]
            ),
            'count' => $repo->fetchCount($query),
            'count-unfiltered' => $repo->hasRows($unfilteredQuery),
        ];
    }
}
