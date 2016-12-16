<?php

/**
 * Task
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Task\Task as Entity;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\System\SubCategory as SubCategoryEntity;
use Doctrine\ORM\Query;

/**
 * Task
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Task extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Fetch a list for an irfo organisation
     *
     * @param int|\Dvsa\Olcs\Api\Entity\Organisation\Organisation $organisation
     *
     * @return array
     */
    public function fetchByIrfoOrganisation($organisation)
    {
        $doctrineQb = $this->createQueryBuilder();

        $doctrineQb->andWhere($doctrineQb->expr()->eq($this->alias . '.irfoOrganisation', ':organisaion'))
            ->setParameter('organisaion', $organisation);

        return $doctrineQb->getQuery()->getResult();
    }

    /**
     * Fetch a list for a Transport Manager
     *
     * @param int|\Dvsa\Olcs\Api\Entity\Tm\TransportManager $transportManager
     *
     * @return array
     */
    public function fetchByTransportManager($transportManager)
    {
        $doctrineQb = $this->createQueryBuilder();

        $doctrineQb->andWhere($doctrineQb->expr()->eq($this->alias . '.transportManager', ':transportManager'))
            ->setParameter('transportManager', $transportManager);

        return $doctrineQb->getQuery()->getResult();
    }

    /**
     * Fetch a list for a user
     *
     * @param int|\Dvsa\Olcs\Api\Entity\User\User $user
     * @param bool $open Only get tasks that are open
     *
     * @return array
     */
    public function fetchByUser($user, $open = false)
    {
        $doctrineQb = $this->createQueryBuilder();

        $doctrineQb->andWhere($doctrineQb->expr()->eq($this->alias . '.assignedToUser', ':user'))
            ->setParameter('user', $user);

        if ($open) {
            $doctrineQb->andWhere($doctrineQb->expr()->eq($this->alias . '.isClosed', ':isClosed'));
            $doctrineQb->setParameter('isClosed', 'N');
        }

        return $doctrineQb->getQuery()->getResult();
    }

    /**
     * Fetch a single task record belonging to Case and Transport Manager, filtered by subcategory
     *
     * @param int|\Dvsa\Olcs\Api\Entity\Cases\Cases $case
     * @param int|\Dvsa\Olcs\Api\Entity\Tm\TransportManager $transportManager
     * @param string $subCategory
     *
     * @return mixed
     */
    public function fetchForTmCaseDecision(
        $case,
        $transportManager,
        $subCategory = ''
    ) {
        $category = CategoryEntity::CATEGORY_TRANSPORT_MANAGER;

        $doctrineQb = $this->createQueryBuilder();

        $doctrineQb->andWhere($doctrineQb->expr()->eq($this->alias . '.transportManager', ':transportManager'))
            ->setParameter('transportManager', $transportManager);
        $doctrineQb->andWhere($doctrineQb->expr()->eq($this->alias . '.case', ':case'))
            ->setParameter('case', $case);
        $doctrineQb->andWhere($doctrineQb->expr()->eq($this->alias . '.category', ':category'))
            ->setParameter('category', $category);

        if (!empty($subCategory)) {
            $doctrineQb->andWhere($doctrineQb->expr()->eq($this->alias . '.subCategory', ':subCategory'))
                ->setParameter('subCategory', $subCategory);
        }

        $result = $doctrineQb->getQuery()->getSingleResult(Query::HYDRATE_OBJECT);

        return $result;
    }

    /**
     * Fetch a single task record belonging to Submission
     *
     * @param int|\Dvsa\Olcs\Api\Entity\Submission\Submission $submission
     *
     * @return mixed
     */
    public function fetchAssignedToSubmission($submission)
    {
        $category = CategoryEntity::CATEGORY_SUBMISSION;
        $subCategory = Entity::SUBCATEGORY_SUBMISSION_ASSIGNMENT;

        $doctrineQb = $this->createQueryBuilder();

        $doctrineQb->andWhere($doctrineQb->expr()->eq($this->alias . '.submission', ':submission'))
            ->setParameter('submission', $submission);
        $doctrineQb->andWhere($doctrineQb->expr()->eq($this->alias . '.category', ':category'))
            ->setParameter('category', $category);
        $doctrineQb->andWhere($doctrineQb->expr()->eq($this->alias . '.subCategory', ':subCategory'))
            ->setParameter('subCategory', $subCategory);
        $doctrineQb->andWhere($doctrineQb->expr()->eq($this->alias . '.isClosed', 0));
        $result = $doctrineQb->getQuery()->getOneOrNullResult(Query::HYDRATE_OBJECT);

        return $result;
    }

    /**
     * Flag applicable tasks as urgent
     *
     * @return int Number of tasks updated
     */
    public function flagUrgentsTasks()
    {
        /** @var \Doctrine\DBAL\Driver\PDOStatement $stmt */
        $stmt = $this->getDbQueryManager()
            ->get('Task/FlagUrgentTasks')
            ->execute();

        $updatedTasks = $stmt->fetchColumn(0);

        return $updatedTasks;
    }
}
