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
        // @TODO port this to dql
        $now = new \DateTime();
        throw new \Exception('todo '. __METHOD__);
        $query = [
            'status' => Entity::STATUS_QUEUED,
            'limit' => 1,
            'sort' => 'createdOn',
            'order' => 'ASC',
            'processAfterDate' => [
                'NULL',
                '<=' . $now
            ]
        ];

        if ($type !== null) {
            $query['type'] = $type;
        }

        $results = $this->get($query, $this->itemBundle);

        if (empty($results['Results'])) {
            return null;
        }

        $result = $results['Results'][0];
        $result['attempts']++;

        $data = [
            'id' => $result['id'],
            'version' => $result['version'],
            'status' => self::STATUS_PROCESSING,
            'attempts' => $result['attempts']
        ];

        try {
            $this->save($data);
            $result['version']++;
        } catch (ResourceConflictException $ex) {
            return null;
        }

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
