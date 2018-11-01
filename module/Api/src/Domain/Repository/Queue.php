<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Queue\Queue as Entity;
use Dvsa\Olcs\Api\Rbac\PidIdentityProvider;

/**
 * Queue
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class Queue extends AbstractRepository
{
    protected $entity = Entity::class;

    protected $alias = 'q';

    /**
     * This custom method will enqueue a message for all active organisations.
     * There are potentially tens of thousands of records so uses an
     * INSERT...SELECT query directly, for performance reasons.
     *
     * @param string $type type
     *
     * @return boolean
     */
    public function enqueueAllOrganisations($type)
    {
        /**
         * @var \Doctrine\DBAL\Connection
         */
        $db = $this->getEntityManager()->getConnection();

        $query = <<<SQL
INSERT INTO `queue` (`status`, `type`, `options`, `created_by`, `last_modified_by`, `created_on`)
SELECT DISTINCT 'que_sts_queued',
                ?,
                CONCAT('{"companyNumber":"', UPPER(o.company_or_llp_no), '"}'),
                ?,
                ?,
                NOW()
FROM organisation o
INNER JOIN licence l ON o.id=l.organisation_id
WHERE l.status IN ('lsts_consideration',
                   'lsts_suspended',
                   'lsts_valid',
                   'lsts_curtailed',
                   'lsts_granted')
  AND o.company_or_llp_no IS NOT NULL
  AND o.type IN ('org_t_rc', 'org_t_llp')
ORDER BY o.company_or_llp_no;
SQL;
        $stmt = $db->prepare($query);
        $params = array($type, PidIdentityProvider::SYSTEM_USER, PidIdentityProvider::SYSTEM_USER);

        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Enqueue CNS
     *
     * @param array $licences licences
     *
     * @return int
     */
    public function enqueueContinuationNotSought($licences)
    {
        /**
         * @var \Doctrine\DBAL\Connection $conn
         */
        $conn = $this->getEntityManager()->getConnection();

        $query = 'INSERT INTO `queue` (`status`, `type`, `options`) VALUES ';

        for ($i = 1, $n = count($licences); $i <= $n; $i++) {
            $query .= "(:status{$i}, :type{$i}, :options{$i}), ";
        }
        $query = trim($query, ', ');

        $params = [];
        $i = 1;
        foreach ($licences as $licence) {
            $params['status' . $i] = Entity::STATUS_QUEUED;
            $params['type' . $i] = Entity::TYPE_CNS;
            $params['options' . $i] = '{"id":' . $licence['id'] . ',"version":' . $licence['version'] . '}';
            $i++;
        }

        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Get next item
     * AND increment attempts
     *
     * @param array $includeTypes Types to include, default include all
     * @param array $excludeTypes Types to exclude, default exclude none
     *
     * @return Entity
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function getNextItem(array $includeTypes = [], array $excludeTypes = [])
    {
        $qb = $this->getNextItemQueryBuilder();

        $result = $this->getNextItemWithQueryBuilder($includeTypes, $excludeTypes, $qb);

        if (!is_null($result)) {
            $result->incrementAttempts();
            $result->setStatus($this->getRefdataReference(Entity::STATUS_PROCESSING));
            $this->save($result);
        }

        return $result;
    }

    /**
     * Fetch next item even if postponed
     * and DO NOT increment attempts
     *
     * @param array $includeTypes Types to include, default include all
     * @param array $excludeTypes Types to exclude, default exclude none
     *
     * @return Entity
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function fetchNextItemIncludingPostponed(array $includeTypes = [], array $excludeTypes = [])
    {
        $qb = $this->fetchNextItemIncludingPostponedQueryBuilder();

        return $this->getNextItemWithQueryBuilder($includeTypes, $excludeTypes, $qb);
    }

    /**
     * Get next item
     *
     * @param array        $includeTypes Types to include, default include all
     * @param array        $excludeTypes Types to exclude, default exclude none
     * @param QueryBuilder $qb           Query builder
     *
     * @return Entity
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function getNextItemWithQueryBuilder($includeTypes, $excludeTypes, \Doctrine\ORM\QueryBuilder $qb)
    {

        if (!empty($includeTypes)) {
            $qb
                ->andWhere($qb->expr()->in($this->alias . '.type', ':includeTypes'))
                ->setParameter('includeTypes', $includeTypes);
        }

        if (!empty($excludeTypes)) {
            $qb
                ->andWhere($qb->expr()->notIn($this->alias . '.type', ':excludeTypes'))
                ->setParameter('excludeTypes', $excludeTypes);
        }

        $results = $qb->getQuery()->getResult();

        if (empty($results)) {
            return null;
        }

        return $results[0];
    }

    /**
     * Is there an item of type already queued?
     *
     * @param string $type Queue::TYPE_ contant
     *
     * @return bool
     */
    public function isItemTypeQueued($type)
    {
        $qb = $this->getNextItemQueryBuilder();
        $qb->andWhere($qb->expr()->eq($this->alias . '.type', ':type'))
            ->setParameter('type', $type);

        $results = $qb->getQuery()->getArrayResult();

        return !empty($results);
    }

    /**
     * Is there an item of type/status already in the queue
     *
     * @param array $types    Queue::TYPE_
     * @param array $statuses Queue::STATUS_
     *
     * @return bool
     */
    public function isItemInQueue(array $types, array $statuses)
    {
        $qb = $this->createQueryBuilder();

        $qb
            ->select($this->alias . '.id')
            ->andWhere($qb->expr()->in($this->alias . '.type', ':types'))
            ->andWhere($qb->expr()->in($this->alias . '.status', ':statuses'))
            ->setParameter('types', $types)
            ->setParameter('statuses', $statuses)
            ->setMaxResults(1);

        $results = $qb->getQuery()->getArrayResult();

        return !empty($results);
    }

    /**
     * Get the QueryBuilder for getting the next item
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getNextItemQueryBuilder()
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
            ->order('id', 'ASC');

        $now = new DateTime();
        $qb
            ->andWhere($qb->expr()->eq($this->alias . '.status', ':statusId'))
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->lte($this->alias . '.processAfterDate', ':processAfter'),
                    $qb->expr()->isNull($this->alias . '.processAfterDate')
                )
            )
            ->setParameter('statusId', Entity::STATUS_QUEUED)
            ->setParameter('processAfter', $now)
            ->setMaxResults(1);

        return $qb;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function fetchNextItemIncludingPostponedQueryBuilder()
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
            ->order($this->alias . '.processAfterDate', 'ASC');

        $qb
            ->andWhere($qb->expr()->eq($this->alias . '.status', ':statusId'))
            ->setParameter('statusId', Entity::STATUS_QUEUED)
            ->setMaxResults(1);

        return $qb;
    }
}
