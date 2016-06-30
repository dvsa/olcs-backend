<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\TaskAllocationRule;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Get List of Task Allocation Rules
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class GetList extends AbstractQueryHandler
{
    protected $repoServiceName = 'TaskAllocationRule';

    /**
     * @param \Dvsa\Olcs\Transfer\Query\TaskAllocationRule\GetList $query
     * @return array
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var  \Dvsa\Olcs\Api\Domain\Repository\TaskAllocationRule $repo */
        $repo = $this->getRepo();

        $repo->disableSoftDeleteable();

        $result = $repo->fetchList($query, \Doctrine\ORM\Query::HYDRATE_OBJECT);

        //  remove deleted users
        /** @var  Entity\Task\TaskAllocationRule $rule */
        foreach ($result as $rule) {
            $this->cleanDeletedUser($rule);

            /** @var Entity\Task\TaskAlphaSplit $split */
            foreach ($rule->getTaskAlphaSplits() as $split) {
                $this->cleanDeletedUser($split);
            }
        }

        return [
            'result' => $this->resultList(
                $result,
                [
                    'category',
                    'team',
                    'user' => ['contactDetails' => ['person']],
                    'trafficArea',
                    'taskAlphaSplits' => ['user' => ['contactDetails' => ['person']]],
                ]
            ),
            'count' => $repo->fetchCount($query),
            'count-unfiltered' => $repo->hasRows($query),
        ];
    }

    /**
     * Remove user entity from entity if user has DeletedDate not null (soft-deleted)
     *
     * @param Entity\Task\TaskAllocationRule|Entity\Task\TaskAlphaSplit $entity
     */
    private function cleanDeletedUser($entity)
    {
        $user = $entity->getUser();

        if ($user !== null && $user->getDeletedDate() !== null) {
            $entity->setUser(null);
        }
    }
}
