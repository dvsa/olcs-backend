<?php

/**
 * Companies House custom queue operations
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Olcs\Db\Service\CompaniesHouse;
use Zend\ServiceManager\AbstractPluginManager;


/**
 * Companies House custom queue operations
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class Queue extends AbstractPluginManager
{

    /**
     * This custom method will enqueue a message for all active organisations.
     * There are potentially tens of thousands of records so uses an
     * INSERT...SELECT query directly, for performance reasons.
     *
     * @return boolean|string
     */
    public function enqueueActiveOrganisations($type)
    {
        /**
         * @var \Doctrine\ORM\EntityManager
         */
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');

        /**
         * @var \Doctrine\DBAL\Connection
         */
        $db = $em->getConnection();

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

    public function getServiceLocator()
    {
        return $this->creationContext;
    }

}
