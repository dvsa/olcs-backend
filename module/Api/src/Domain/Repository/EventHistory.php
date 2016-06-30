<?php
/**
 * EventHistory
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\EventHistory\EventHistory as EventHistoryEntity;
use Dvsa\Olcs\Transfer\Query\Processing\History as HistoryDTO;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * EventHistory
 */
class EventHistory extends AbstractRepository
{
    protected $entity = EventHistoryEntity::class;

    protected $fieldsToExclude = [
        'hist_id',
        'hist_timestamp',
        'hist_transaction_type',
        'hist_db_user',
        'created_by',
        'last_modified_by',
        'created_on',
        'last_modified_on',
        'version',
        'olbs_key',
        'olbs_type'
    ];

    /**
     * @param QueryBuilder $qb
     * @param HistoryDTO $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        if ($query->getCase() !== null) {
            $qb->orWhere($qb->expr()->eq($this->alias . '.case', ':caseId'));
            $qb->setParameter('caseId', $query->getCase());
        }

        if ($query->getLicence() !== null) {
            $qb->orWhere($qb->expr()->eq($this->alias . '.licence', ':licenceId'));
            $qb->setParameter('licenceId', $query->getLicence());
        }

        if ($query->getOrganisation() !== null) {
            $qb->orWhere($qb->expr()->eq($this->alias . '.organisation', ':organisationId'));
            $qb->setParameter('organisationId', $query->getOrganisation());
        }

        if ($query->getTransportManager() !== null) {
            $qb->orWhere($qb->expr()->eq($this->alias . '.transportManager', ':transportManagerId'));
            $qb->setParameter('transportManagerId', $query->getTransportManager());
        }

        if ($query->getUser() !== null) {
            $qb->orWhere($qb->expr()->eq($this->alias . '.user', ':userId'));
            $qb->setParameter('userId', $query->getUser());
        }

        if ($query->getApplication() !== null) {
            $qb->orWhere($qb->expr()->eq($this->alias . '.application', ':applicationId'));
            $qb->setParameter('applicationId', $query->getApplication());
        }

        $this->getQueryBuilder()->modifyQuery($qb)->with('eventHistoryType')->withUser();
    }

    /**
     * Apply list join
     *
     * @param QueryBuilder $qb
     */
    protected function applyListJoins(QueryBuilder $qb)
    {
        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefData()
            ->with('case')
            ->with('licence')
            ->with('application')
            ->with('organisation')
            ->with('transportManager')
            ->with('busReg');
    }

    /**
     * Fetch a list for an organisation
     *
     * @param int|\Dvsa\Olcs\Api\Entity\Organisation\Organisation $organisation
     *
     * @return array
     */
    public function fetchByOrganisation($organisation)
    {
        $doctrineQb = $this->createQueryBuilder();

        $doctrineQb->andWhere($doctrineQb->expr()->eq($this->alias . '.organisation', ':organisaion'))
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
     * Fetch event history details
     *
     * @param int $id
     * @param int $version
     * @param string $table
     * @return array
     */
    public function fetchEventHistoryDetails($id, $version, $table)
    {
        $eventDetailsQuery = $this->getDbQueryManager()->get('EventHistory\GetEventHistoryDetails');
        $eventDetailsQuery->setHistoryTable($table);
        $stmt = $eventDetailsQuery->execute(['id' => $id, 'version' => [$version, $version - 1]]);
        $eventHistory = $stmt->fetchAll();
        $returnValues = [];

        if (count($eventHistory)) {
            $cleanValues = [];
            foreach ($eventHistory as $hist) {
                foreach ($this->fieldsToExclude as $field) {
                    if (array_key_exists($field, $hist)) {
                        unset($hist[$field]);
                    }
                }
                $cleanValues[] = $hist;
            }
            $keys = array_keys($cleanValues[0]);
            for ($i = 0; $i < count($keys); $i++) {
                if (isset($cleanValues[1]) && $cleanValues[0][$keys[$i]] === $cleanValues[1][$keys[$i]]) {
                    continue;
                }
                $element['newValue'] = $cleanValues[0][$keys[$i]];
                $element['oldValue'] = isset($cleanValues[1][$keys[$i]]) ? $cleanValues[1][$keys[$i]] : '';
                $element['name'] = $keys[$i];
                $returnValues[] = $element;
            }
        }

        return $returnValues;
    }
}
