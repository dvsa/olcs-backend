<?php

/**
 * Checklists
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Olcs\Db\Service\ContinuationDetail;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Checklists
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Checklists implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    const CONTINUATION_DETAIL_STATUS_PRINTING = 'con_det_sts_printing';

    /**
     * Generate checklists
     *
     * @param array $data
     */
    public function generate($data)
    {
        /* @var $em Doctrine\ORM\EntityManagerInterface */
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $qb = $em->createQueryBuilder();
        $qb->select('m')
            ->from('\Olcs\Db\Entity\ContinuationDetail', 'm')
            ->where($qb->expr()->in('m.id', $data));

        $results = $qb->getQuery()->execute();

        $em->beginTransaction();

        try {
            foreach ($results as $row) {
                $row->setStatus(self::CONTINUATION_DETAIL_STATUS_PRINTING);
            }
        }

        // Update statuses

        // Add to queue
    }
}
