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
    protected $extraRepos = ['Cases'];

    public function handleQuery(QueryInterface $query)
    {
        // The case / licence business logic.
        if (null !== $query->getCase()) {

            /** @var \Dvsa\Olcs\Transfer\Query\Processing\NoteList $query */
            $caseId = $query->getCase();

            $case = $this->getRepo('Cases')->fetchById($caseId);

            if ($case->getLicence() !== null) {

                $licenceId = $case->getLicence()->getId();
                $data = $query->getArrayCopy();
                $data['licence'] = $licenceId;
                // Replace existing
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
