<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Queue\Queue as Entity;

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
     * @return boolean|string
     */
    public function enqueueAllOrganisations($type)
    {
        /**
         * @var \Doctrine\DBAL\Connection
         */
        $db = $this->getEntityManager()->getConnection();

        $query = <<<SQL
INSERT INTO `queue` (`status`, `type`, `options`)
SELECT DISTINCT 'que_sts_queued',
                ?,
                CONCAT('{"companyNumber":"', o.company_or_llp_no, '"}')
FROM organisation o
INNER JOIN licence l ON o.id=l.organisation_id
WHERE l.status IN ('lsts_consideration',
                   'lsts_suspended',
                   'lsts_valid',
                   'lsts_curtailed',
                   'lsts_granted')
  AND o.company_or_llp_no IS NOT NULL
ORDER BY o.company_or_llp_no;
SQL;
        $stmt = $db->prepare($query);
        $params = array($type);

        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function getNextItem($type = null)
    {
        /* @var \Doctrine\Orm\QueryBuilder $qb*/
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
            ->order('createdOn', 'ASC');

        $now = new \DateTime();
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

        if ($type !== null) {
            $qb
                ->andWhere($qb->expr()->eq($this->alias . '.type', ':typeId'))
                ->setParameter('typeId', $type);
        }

        $results = $qb->getQuery()->execute();

        if (empty($results)) {
            return null;
        }

        $result = $results[0];
        $result->incrementAttempts();
        $result->setStatus($this->getRefDataReference(Entity::STATUS_PROCESSING));
        $this->save($result);

        return $result;
    }

    public function retry($item)
    {
        $item['status'] = self::STATUS_QUEUED;

        $this->save($item);
    }

    public function complete($item)
    {
        $item['status'] = self::STATUS_COMPLETE;

        $this->save($item);
    }

    public function failed($item)
    {
        $item['status'] = self::STATUS_FAILED;

        $this->save($item);
    }
}
