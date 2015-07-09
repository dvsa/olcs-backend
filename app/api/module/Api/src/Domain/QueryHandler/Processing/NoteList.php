<?php

/**
 * Note
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Processing;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\Note as NoteRepository;
use Dvsa\Olcs\Transfer\Query\Processing\NoteList as NoteListQuery;

/**
 * Note
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
            $case = $this->getRepo('Cases')->fetchById($caseId);

            if ($case->getLicence() !== null) {

                $licenceId = $case->getLicence()->getId();
                $data = $query->getArrayCopy();
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

        return [
            'result' => $repo->fetchList($query),
            'count' => $repo->fetchCount($query)
        ];
    }
}
