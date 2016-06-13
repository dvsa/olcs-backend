<?php

/**
 * Task Search View
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\System\RefData as RefDataEntity;
use Dvsa\Olcs\Api\Entity\View\TaskSearchView as Entity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Task Search View
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TaskSearchView extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Setting to false removes the unnecessary DISTINCT clause from pagination queries
     * @see http://doctrine-orm.readthedocs.io/projects/doctrine-orm/en/latest/tutorials/pagination.html
     *
     * @var bool
     */
    protected $fetchJoinCollection = false;

    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        $assignedToUser = $query->getAssignedToUser();

        if (!empty($assignedToUser)) {
            $qb->andWhere($qb->expr()->eq('m.assignedToUser', $assignedToUser));
        }

        $assignedToTeam = $query->getAssignedToTeam();
        if (!empty($assignedToTeam)) {
            $qb->andWhere($qb->expr()->eq('m.assignedToTeam', $assignedToTeam));
        }

        $category = $query->getCategory();
        if (!empty($category)) {
            $qb->andWhere($qb->expr()->eq('m.category', $category));
        }

        $taskSubCategory = $query->getTaskSubCategory();
        if (!empty($taskSubCategory)) {
            $qb->andWhere($qb->expr()->eq('m.taskSubCategory', $taskSubCategory));
        }

        $actionDate = $query->getDate();
        if (!empty($actionDate) && $actionDate === RefDataEntity::TASK_ACTION_DATE_TODAY) {
            $qb->andWhere($qb->expr()->lte('m.actionDate', ':actionDate'));
            $qb->setParameter('actionDate', date('Y-m-d'));
        }

        $status = $query->getStatus();
        if (!empty($status) && $status !== 'tst_all') {
            $qb->andWhere($qb->expr()->eq('m.isClosed', $status == 'tst_closed' ? 1 : 0));
        }

        $urgent = $query->getUrgent();
        if ($urgent !== null && $urgent) {
            $qb->andWhere($qb->expr()->eq('m.urgent', 1));
        }

        $idExpressions = [];

        if ($query->getLicence() !== null) {
            $idExpressions[] = $qb->expr()->eq(
                'm.licenceId', ':licence'
            );
            $qb->setParameter('licence', $query->getLicence());
        }

        if ($query->getTransportManager() !== null) {
            $idExpressions[] = $qb->expr()->eq(
                'm.transportManagerId', ':tm'
            );
            $qb->setParameter('tm', $query->getTransportManager());
        }

        if ($query->getCase() !== null) {
            $idExpressions[] = $qb->expr()->eq(
                'm.caseId', ':case'
            );
            $qb->setParameter('case', $query->getCase());
        }

        if ($query->getApplication() !== null) {
            $idExpressions[] = $qb->expr()->eq(
                'm.applicationId', ':application'
            );
            $qb->setParameter('application', $query->getApplication());
        }

        if ($query->getBusReg() !== null) {
            $idExpressions[] = $qb->expr()->eq(
                'm.busRegId', ':busReg'
            );
            $qb->setParameter('busReg', $query->getBusReg());
        }

        if ($query->getOrganisation() !== null) {
            $idExpressions[] = $qb->expr()->eq(
                $this->alias . '.irfoOrganisationId', ':organisation'
            );
            $qb->setParameter('organisation', $query->getOrganisation());
        }

        if (!empty($idExpressions)) {
            $expr = $qb->expr();

            $qb->andWhere(
                call_user_func_array([$expr, 'orX'], $idExpressions)
            );
        }
    }
}
